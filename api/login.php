<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../utils/api.php');
require_once(__DIR__ . '/../utils/api_serializer.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');

$session = new Session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson([
        'success' => false,
        'message' => 'Method not allowed.'
    ], 405);
}

$input = getJsonInput();

$email = trim((string)($input['email'] ?? ''));
$password = (string)($input['password'] ?? '');

if ($email === '' || $password === '') {
    sendJson([
        'success' => false,
        'message' => 'Email and password are required.'
    ], 400);
}

$db = getDatabaseConnection();

$user = Users::getUserWithPassword($db, $email, $password);

if ($user === null) {
    sendJson([
        'success' => false,
        'message' => 'Invalid email or password.'
    ], 401);
}

session_regenerate_id(true);

$session->setId($user->getUserId());
$session->setName($user->getName());
$session->setUsername($user->getUserName());
$session->setEmail($user->getEmail());
$session->setRole($user->getRole());

sendJson([
    'success' => true,
    'message' => 'Logged in successfully.',
    'user' => userToJson($user)
]);