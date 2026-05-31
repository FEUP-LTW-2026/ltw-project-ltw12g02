<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../utils/csrf.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');

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

$name = trim($_POST['name'] ?? '');
$username = trim($_POST['username'] ?? '');

$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if ($name === '' || $username === '') {
    $session->addMessage('error', 'Name and username cannot be empty!');
    header('Location: ../pages/profile.php');
    exit;
}

if (Users::usernameExistsForOtherUser($db, $username, $userId)) {
    $session->addMessage('error', 'That username is already being used!');
    header('Location: ../pages/profile.php');
    exit;
}

$passwordHash = $user->getPasswordHash();

$wantsToChangePassword =
    $currentPassword !== '' ||
    $newPassword !== '' ||
    $confirmPassword !== '';

if ($wantsToChangePassword) {
    if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
        $session->addMessage('error', 'Please fill in all password fields!');
        header('Location: ../pages/profile.php');
        exit;
    }

    if (!password_verify($currentPassword, $user->getPasswordHash())) {
        $session->addMessage('error', 'Current password is wrong!');
        header('Location: ../pages/profile.php');
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        $session->addMessage('error', 'New passwords do not match!');
        header('Location: ../pages/profile.php');
        exit;
    }

    if (strlen($newPassword) < 6) {
        $session->addMessage('error', 'New password must have at least 6 characters!');
        header('Location: ../pages/profile.php');
        exit;
    }

    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
}

$user->updateProfileData($db, $name, $username, $passwordHash);

if (
    isset($_FILES['profile_image']) &&
    $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE
) {
    $image = $_FILES['profile_image'];

    if ($image['error'] !== UPLOAD_ERR_OK) {
        $session->addMessage('error', 'Error uploading image!');
        header('Location: ../pages/profile.php');
        exit;
    }

    $imageInfo = getimagesize($image['tmp_name']);

    if ($imageInfo === false || !isset($imageInfo['mime'])) {
        $session->addMessage('error', 'Invalid image type!');
        header('Location: ../pages/profile.php');
        exit;
    }

    $extension = match ($imageInfo['mime']) {
        'image/jpeg', 'image/pjpeg' => 'jpg',
        'image/png', 'image/x-png' => 'png',
        'image/webp' => 'webp',
        default => null
    };

    if ($extension === null) {
        $session->addMessage(
            'error',
            'Only JPG, PNG and WEBP images are allowed! Detected: ' . $imageInfo['mime']
        );

        header('Location: ../pages/profile.php');
        exit;
    }

    $profileImage = bin2hex(random_bytes(16)) . '.' . $extension;

    $destination = __DIR__ . '/../assets/users/' . $profileImage;

    if (!move_uploaded_file($image['tmp_name'], $destination)) {
        $session->addMessage('error', 'Could not save image!');
        header('Location: ../pages/profile.php');
        exit;
    }

    $user->updateProfileImage($db, $profileImage);
}

$session->setName($name);
$session->setUsername($username);

$session->addMessage('success', 'Profile updated successfully!');

header('Location: ../pages/profile.php');
exit;
?>