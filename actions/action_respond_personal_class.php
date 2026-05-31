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

$trainer = Trainers::getTrainerByUserId($db, $userId);

if ($trainer === null) {
    $session->addMessage('error', 'Only trainers can respond to personal class requests.');
    header('Location: ../pages/profile.php');
    exit;
}

$personalClassId = filter_input(INPUT_POST, 'personal_class_id', FILTER_VALIDATE_INT);
$responseStatus = trim($_POST['response_status'] ?? '');
$trainerResponse = trim($_POST['trainer_response'] ?? '');

if ($personalClassId === false || $personalClassId === null) {
    $session->addMessage('error', 'Invalid personal class request.');
    header('Location: ../pages/profile.php');
    exit;
}

if (!in_array($responseStatus, ['accepted', 'rejected'], true)) {
    $session->addMessage('error', 'Invalid response.');
    header('Location: ../pages/profile.php');
    exit;
}

$personalClass = PersonalClass::getPersonalClass($db, $personalClassId);

if ($personalClass === null) {
    $session->addMessage('error', 'Personal class request not found.');
    header('Location: ../pages/profile.php');
    exit;
}

if ($personalClass->getTrainerId() !== $trainer->getTrainerId()) {
    $session->addMessage('error', 'You cannot respond to this request.');
    header('Location: ../pages/profile.php');
    exit;
}

if ($personalClass->getStatus() !== 'pending') {
    $session->addMessage('error', 'This request has already been answered.');
    header('Location: ../pages/profile.php');
    exit;
}

if ($trainerResponse === '') {
    $trainerResponse = null;
}

try {
    if ($responseStatus === 'accepted') {
        $personalClass->acceptPersonalClass($db, $trainerResponse);
        $session->addMessage('success', 'Personal class request accepted.');
    } else {
        $personalClass->rejectPersonalClass($db, $trainerResponse);
        $session->addMessage('success', 'Personal class request rejected.');
    }
} catch (PDOException $e) {
    $session->addMessage('error', 'Could not respond to personal class request.');
}

header('Location: ../pages/profile.php');
exit;
?>