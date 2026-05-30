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
    header('Location: ../pages/admin_users.php');
    exit;
}

evaluateCSRF($_POST['token'] ?? '');

$userId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$role = $_POST['role'] ?? '';
$confirmationName = trim($_POST['confirmation_name'] ?? '');

if ($userId === false || $userId === null) {
    header('Location: ../pages/admin_users.php');
    exit;
}

$allowedRoles = ['member', 'trainer', 'admin'];

if (!in_array($role, $allowedRoles, true)) {
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

$user->changeRole($role, $db);

header('Location: ../pages/admin_edit_user.php?id=' . $userId);
exit;