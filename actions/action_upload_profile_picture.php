<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
$session = new Session();

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');

if (!$session->isLoggedIn()) {
    header('Location: ../pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/profile.php');
    exit;
}

if (!isset($_FILES['profile_picture'])) {
    $session->addMessage('error', 'No image uploaded!');
    header('Location: ../pages/profile.php');
    exit;
}

$image = $_FILES['profile_picture'];

if ($image['error'] !== UPLOAD_ERR_OK) {
    $session->addMessage('error', 'Error uploading image!');
    header('Location: ../pages/profile.php');
    exit;
}

$mimeType = mime_content_type($image['tmp_name']);

$sourceImage = match ($mimeType) {
    'image/jpeg' => imagecreatefromjpeg($image['tmp_name']),
    'image/png' => imagecreatefrompng($image['tmp_name']),
    'image/webp' => imagecreatefromwebp($image['tmp_name']),
    default => false
};

if ($sourceImage === false) {
    $session->addMessage('error', 'Invalid image type!');
    header('Location: ../pages/profile.php');
    exit;
}

$filename = bin2hex(random_bytes(16)) . '.png';

$destination = __DIR__ . '/../assets/users/' . $filename;

if (!imagepng($sourceImage, $destination)) {
    $session->addMessage('error', 'Could not save image!');
    header('Location: ../pages/profile.php');
    exit;
}

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

$user->updateProfileImage($db, $filename);

$session->addMessage('success', 'Profile picture updated!');

header('Location: ../pages/profile.php');
exit;