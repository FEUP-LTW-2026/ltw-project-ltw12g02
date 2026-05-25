<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../utils/api.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../utils/api_serializer.php');


$session = new Session();

if (!$session->isLoggedIn()) {
    sendJson([
        'success' => false,
        'message' => 'You must be logged in.'
    ], 401);
}

if ($session->getRole() !== 'admin') {
    sendJson([
        'success' => false,
        'message' => 'Only admins can access users.'
    ], 403);
}

$db = getDatabaseConnection();

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') {
    sendJson([
        'success' => false,
        'message' => 'Method not allowed.'
    ], 405);
}

$hasId = isset($_GET['id']);
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($hasId && $id === false) {
    sendJson([
        'success' => false,
        'message' => 'Invalid user id.'
    ], 400);
}

if ($id !== null && $id !== false) {
    $user = Users::getUser($db, $id);

    if ($user === null) {
        sendJson([
            'success' => false,
            'message' => 'User not found.'
        ], 404);
    }

    sendJson(userToJson($user));
}

$users = Users::getAllUsers($db);

$result = usersToJson($users);

sendJson($result);