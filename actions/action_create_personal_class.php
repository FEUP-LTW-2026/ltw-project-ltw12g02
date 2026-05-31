<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../utils/csrf.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/trainers.class.php');
require_once(__DIR__ . '/../database/personalclass.class.php');

$session = new Session();

if (!$session->isLoggedIn()) {
    header('Location: ../pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/profile.php');
    exit;
}

evaluateCSRF($_POST['token'] ?? '');

$db = getDatabaseConnection();

$userId = $session->getId();

if ($userId === null) {
    $session->logout();
    header('Location: ../pages/login.php');
    exit;
}

$user = Users::getUser($db, $userId);

if ($user === null) {
    $session->logout();
    header('Location: ../pages/login.php');
    exit;
}

$trainerId = filter_input(INPUT_POST, 'trainer_id', FILTER_VALIDATE_INT);
$durationMinutes = filter_input(INPUT_POST, 'duration_minutes', FILTER_VALIDATE_INT);

$startDateTime = trim($_POST['start_date_time'] ?? '');
$requestMessage = trim($_POST['request_message'] ?? '');

if ($trainerId === false || $trainerId === null || $durationMinutes === false || $durationMinutes === null) {
    $session->addMessage('error', 'Invalid personal class request.');
    header('Location: ../pages/profile.php');
    exit;
}

$trainer = Trainers::getTrainer($db, $trainerId);

if ($trainer === null) {
    $session->addMessage('error', 'Trainer not found.');
    header('Location: ../pages/profile.php');
    exit;
}

if ($trainer->getUserId() === $userId) {
    $session->addMessage('error', 'You cannot request a personal class with yourself.');
    header('Location: ../pages/profile.php');
    exit;
}

if ($startDateTime === '') {
    $session->addMessage('error', 'Please choose a date and time.');
    header('Location: ../pages/profile.php');
    exit;
}

if ($durationMinutes <= 0) {
    $session->addMessage('error', 'Invalid duration.');
    header('Location: ../pages/profile.php');
    exit;
}

if (!in_array($durationMinutes, [30, 45, 60, 90], true)) {
    $session->addMessage('error', 'Invalid duration.');
    header('Location: ../pages/profile.php');
    exit;
}

if ($requestMessage === '') {
    $requestMessage = null;
}

try {
    PersonalClass::createPersonalClass(
        $db,
        $userId,
        $trainerId,
        $startDateTime,
        $durationMinutes,
        $requestMessage
    );

    $session->addMessage('success', 'Personal class request sent.');
} catch (InvalidArgumentException $e) {
    $session->addMessage('error', $e->getMessage());
} catch (PDOException $e) {
    $session->addMessage('error', 'Could not create personal class request.');
}

header('Location: ../pages/trainer_profile.php?id=' . $trainerId);
exit;
?>