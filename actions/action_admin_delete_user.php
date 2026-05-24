<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');

$session = new Session();

if (!$session->isLoggedIn() || $session->getRole() !== 'admin') {
    header('Location: ../pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/admin_users.php');
    exit;
}

$userId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$confirmationName = trim($_POST['confirmation_name'] ?? '');

if ($userId === false || $userId === null) {
    header('Location: ../pages/admin_users.php');
    exit;
}

if ($userId === $session->getId()) {
    header('Location: ../pages/admin_edit_user.php?id=' . $userId);
    exit;
}

$db = getDatabaseConnection();

$user = Users::getUser($db, $userId);

if ($user === null) {
    header('Location: ../pages/admin_users.php');
    exit;
}

if ($confirmationName !== $user->getName()) {
    header('Location: ../pages/admin_edit_user.php?id=' . $userId);
    exit;
}

try {
    $user->deleteUser($db);

    header('Location: ../pages/admin_users.php');
    exit;

} catch (PDOException $e) {
    header('Location: ../pages/admin_edit_user.php?id=' . $userId);
    exit;
}