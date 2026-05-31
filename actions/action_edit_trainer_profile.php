<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../utils/csrf.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/trainers.class.php');

$session = new Session();

if (!$session->isLoggedIn()) {
    header('Location: ../pages/login.php');
    exit();
}

$db = getDatabaseConnection();

$user = Users::getUser($db, $session->getId());

if ($user === null) {
    $session->logout();
    header('Location: ../pages/login.php');
    exit();
}

if ($user->getRole() !== 'trainer') {
    $session->addMessage('error', 'Only trainers can edit trainer profile information.');
    header('Location: ../pages/profile.php');
    exit();
}

evaluateCSRF($_POST['token'] ?? '');

$bio = trim($_POST['bio'] ?? '');
$specializations = trim($_POST['specializations'] ?? '');
$certifications = trim($_POST['certifications'] ?? '');

$stmt = $db->prepare('
    UPDATE Trainers
    SET Bio = ?,
        Specializations = ?,
        Certifications = ?
    WHERE UserId = ?
');

$stmt->execute([
    $bio !== '' ? $bio : null,
    $specializations !== '' ? $specializations : null,
    $certifications !== '' ? $certifications : null,
    $user->getUserId()
]);

$session->addMessage('success', 'Trainer profile updated successfully.');

header('Location: ../pages/profile.php');
exit();
?>