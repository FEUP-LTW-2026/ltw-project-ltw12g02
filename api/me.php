<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../utils/api.php');
require_once(__DIR__ . '/../utils/api_serializer.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');

$session = new Session();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJson([
        'success' => false,
        'message' => 'Method not allowed.'
    ], 405);
}

if (!$session->isLoggedIn()) {
    sendJson([
        'success' => false,
        'message' => 'You must be logged in.'
    ], 401);
}

$db = getDatabaseConnection();

$user = Users::getUser($db, (int)$session->getId());

if ($user === null) {
    sendJson([
        'success' => false,
        'message' => 'User not found.'
    ], 404);
}

sendJson([
    'success' => true,
    'user' => userToJson($user)
]);