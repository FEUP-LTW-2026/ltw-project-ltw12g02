<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
$session = new Session();

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/register.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if ($name === '' || $username === '' || $email === '' || $password === '' || $confirmPassword === '') {
    $session->addMessage('error', 'Please fill in all fields!');
    header('Location: ../pages/register.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $session->addMessage('error', 'Invalid email format!');
    header('Location: ../pages/register.php');
    exit;
}

if ($password !== $confirmPassword) {
    $session->addMessage('error', 'Passwords do not match!');
    header('Location: ../pages/register.php');
    exit;
}

$db = getDatabaseConnection();

if (Users::emailExists($db, $email)) {
    $session->addMessage('error', 'That email is already being used!');
    header('Location: ../pages/register.php');
    exit;
}

if (Users::usernameExists($db, $username)) {
    $session->addMessage('error', 'That username is already being used!');
    header('Location: ../pages/register.php');
    exit;
}

$user = Users::create($db, $name, $username, $email, $password);

$session->setId($user->getUserId());
$session->setName($user->getName());
$session->setUsername($user->getUserName());
$session->setEmail($user->getEmail());
$session->setRole($user->getRole());

$session->addMessage('success', 'Account created successfully!');

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
?>