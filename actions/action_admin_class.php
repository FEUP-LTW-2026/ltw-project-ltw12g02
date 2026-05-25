<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/trainers.class.php');
require_once(__DIR__ . '/../database/workoutclass.class.php');
require_once(__DIR__ . '/../database/workoutclasstype.class.php');

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

if ($action === 'create') {
    createAdminClass($db, $session);
} else if ($action === 'update') {
    updateAdminClass($db, $session);
} else if ($action === 'delete') {
    deleteAdminClass($db, $session);
} else {
    $session->addMessage('error', 'Invalid action.');
}

header('Location: ../pages/admin_classes.php');
exit;


function createAdminClass(PDO $db, Session $session): void {
    $trainerId = filter_input(INPUT_POST, 'trainer_id', FILTER_VALIDATE_INT);
    $classTypeId = filter_input(INPUT_POST, 'class_type_id', FILTER_VALIDATE_INT);
    $capacity = filter_input(INPUT_POST, 'capacity', FILTER_VALIDATE_INT);
    $classDateTime = normalizeAdminClassDateTime(trim($_POST['class_datetime'] ?? ''));

    if ($trainerId === false || $trainerId === null ||
        $classTypeId === false || $classTypeId === null ||
        $capacity === false || $capacity === null ||
        $classDateTime === null) {
        $session->addMessage('error', 'Please fill all fields correctly.');
        return;
    }

    if ($capacity < 1) {
        $session->addMessage('error', 'Capacity must be at least 1.');
        return;
    }

    $trainer = Trainers::getTrainer($db, $trainerId);
    $classType = WorkoutClassType::getWorkoutClassType($db, $classTypeId);

    if ($trainer === null || $classType === null) {
        $session->addMessage('error', 'Trainer or class type not found.');
        return;
    }

    WorkoutClass::createClass(
        $db,
        $trainer->getTrainerId(),
        $classType->getId(),
        $classDateTime,
        $capacity
    );

    $session->addMessage('success', 'Class created successfully.');
}


function updateAdminClass(PDO $db, Session $session): void {
    $classId = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT);
    $trainerId = filter_input(INPUT_POST, 'trainer_id', FILTER_VALIDATE_INT);
    $classTypeId = filter_input(INPUT_POST, 'class_type_id', FILTER_VALIDATE_INT);
    $capacity = filter_input(INPUT_POST, 'capacity', FILTER_VALIDATE_INT);
    $classDateTime = normalizeAdminClassDateTime(trim($_POST['class_datetime'] ?? ''));

    if ($classId === false || $classId === null ||
        $trainerId === false || $trainerId === null ||
        $classTypeId === false || $classTypeId === null ||
        $capacity === false || $capacity === null ||
        $classDateTime === null) {
        $session->addMessage('error', 'Please fill all fields correctly.');
        return;
    }

    if ($capacity < 1) {
        $session->addMessage('error', 'Capacity must be at least 1.');
        return;
    }

    $class = WorkoutClass::getWorkoutClass($db, $classId);
    $trainer = Trainers::getTrainer($db, $trainerId);
    $classType = WorkoutClassType::getWorkoutClassType($db, $classTypeId);

    if ($class === null || $trainer === null || $classType === null) {
        $session->addMessage('error', 'Class, trainer or class type not found.');
        return;
    }

    $class->updateClass(
        $db,
        $trainer->getTrainerId(),
        $classType->getId(),
        $classDateTime,
        $capacity
    );

    $session->addMessage('success', 'Class updated successfully.');
}


function deleteAdminClass(PDO $db, Session $session): void {
    $classId = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT);

    if ($classId === false || $classId === null) {
        $session->addMessage('error', 'Invalid class.');
        return;
    }

    $class = WorkoutClass::getWorkoutClass($db, $classId);

    if ($class === null) {
        $session->addMessage('error', 'Class not found.');
        return;
    }

    WorkoutClass::deleteClass($db, $class->getId());

    $session->addMessage('success', 'Class deleted successfully.');
}


function normalizeAdminClassDateTime(string $dateTime): ?string {
    if ($dateTime === '') {
        return null;
    }

    $dateTime = str_replace('T', ' ', $dateTime);

    $timestamp = strtotime($dateTime);

    if ($timestamp === false) {
        return null;
    }

    return date('Y-m-d H:i:s', $timestamp);
}
?>