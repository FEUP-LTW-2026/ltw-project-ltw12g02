<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/workoutclass.class.php');

$session = new Session();

if (!$session->isLoggedIn()) {
    header('Location: ../pages/login.php');
    exit;
}

$db = getDatabaseConnection();

$user = Users::getUser($db, $session->getId());

if ($user === null || $user->getRole() !== 'admin') {
    header('Location: ../pages/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/admin_classes.php');
    exit;
}

$action = $_POST['action'] ?? '';

try {
    if ($action === 'create') {
        WorkoutClass::createClass(
            $db,
            (int) ($_POST['trainer_id'] ?? 0),
            (int) ($_POST['class_type_id'] ?? 0),
            trim($_POST['class_datetime'] ?? ''),
            (int) ($_POST['capacity'] ?? 0)
        );

        $session->addMessage('success', 'Class created successfully.');

    } else if ($action === 'update') {
        $class = WorkoutClass::getWorkoutClass($db, (int) ($_POST['class_id'] ?? 0));

        if ($class === null) {
            throw new InvalidArgumentException('Class not found.');
        }

        $class->updateClass(
            $db,
            (int) ($_POST['trainer_id'] ?? 0),
            (int) ($_POST['class_type_id'] ?? 0),
            trim($_POST['class_datetime'] ?? ''),
            (int) ($_POST['capacity'] ?? 0)
        );

        $session->addMessage('success', 'Class updated successfully.');

    } else if ($action === 'delete') {
        WorkoutClass::deleteClass(
            $db,
            (int) ($_POST['class_id'] ?? 0)
        );

        $session->addMessage('success', 'Class deleted successfully.');

    } else {
        throw new InvalidArgumentException('Invalid action.');
    }

} catch (InvalidArgumentException $e) {
    $session->addMessage('error', $e->getMessage());

} catch (PDOException $e) {
    $session->addMessage('error', 'Database error while managing class.');
}

header('Location: ../pages/admin_classes.php');
exit;
?>