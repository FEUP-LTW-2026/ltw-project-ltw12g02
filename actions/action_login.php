<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
$session = new Session();

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');


$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    $session->addMessage('error', 'Please fill in all fields!');
    header('Location: ../pages/login.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $session->addMessage('error', 'Invalid email format!');
    header('Location: ../pages/login.php');
    exit;
}

$db = getDatabaseConnection();

$user = Users::getUserWithPassword($db, $email, $password);

if ($user) {
    $session->setId($user->getUserId());
    $session->setName($user->getName());
    $session->setUsername($user->getUserName());
    $session->setEmail($user->getEmail());
    $session->setRole($user->getRole());

    $session->addMessage('success', 'Login successful!');

    header('Location: ../pages/profile.php');
    exit;
}

$session->addMessage('error', 'Wrong email or password!');
header('Location: ../pages/login.php');
exit;
?>