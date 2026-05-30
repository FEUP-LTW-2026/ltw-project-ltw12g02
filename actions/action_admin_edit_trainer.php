<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../utils/csrf.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/trainers.class.php');

$session = new Session();

if (!$session->isLoggedIn() || $session->getRole() !== 'admin') {
    header('Location: ../pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/admin_trainers.php');
    exit;
}

evaluateCSRF($_POST['token'] ?? '');

$trainerId = filter_input(INPUT_POST, 'trainer_id', FILTER_VALIDATE_INT);

if ($trainerId === false || $trainerId === null) {
    $session->addMessage('error', 'Invalid trainer.');
    header('Location: ../pages/admin_trainers.php');
    exit;
}

$bio = trim($_POST['bio'] ?? '');
$specializations = trim($_POST['specializations'] ?? '');
$certifications = trim($_POST['certifications'] ?? '');

if ($bio === '') {
    $session->addMessage('error', 'Bio cannot be empty.');
    header('Location: ../pages/admin_edit_trainer.php?id=' . $trainerId);
    exit;
}

$db = getDatabaseConnection();

$trainer = Trainers::getTrainer($db, $trainerId);

if ($trainer === null) {
    $session->addMessage('error', 'Trainer not found.');
    header('Location: ../pages/admin_trainers.php');
    exit;
}

try {
    $trainer->updateTrainer(
        $db,
        $bio,
        $specializations,
        $certifications
    );

    $session->addMessage('success', 'Trainer profile updated successfully.');

    header('Location: ../pages/admin_edit_trainer.php?id=' . $trainerId);
    exit;

} catch (PDOException $e) {
    $session->addMessage('error', 'Could not update trainer profile.');

    header('Location: ../pages/admin_edit_trainer.php?id=' . $trainerId);
    exit;
}