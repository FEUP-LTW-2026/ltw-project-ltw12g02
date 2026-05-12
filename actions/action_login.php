<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
$session = new Session();

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');

$db = getDatabaseConnection();

$user = Users::getUserWithPassword($db, $_POST['email'], $_POST['password']);

if ($user) {
    $session->setId($user->getUserId());
    $session->setName($user->getName());
    $session->setUsername($user->getUserName());
    $session->setEmail($user->getEmail());
    $session->setRole($user->getRole());

    $session->addMessage('success', 'Login successful!');

    header('Location: ../pages/profile.php');
    exit;
} else {
    $session->addMessage('error', 'Wrong email or password!');

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
?>