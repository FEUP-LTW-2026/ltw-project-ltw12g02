<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
$session = new Session();

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');


header('Content-Type: application/json');

if (!$session->isLoggedIn()) {
    http_response_code(403);

    echo json_encode([
        'success' => false,
        'message' => 'Forbidden'
    ]);

    exit;
}

$db = getDatabaseConnection();

$role = Users::getUser($db,$session->getId())->getRole();



if ($role !== 'admin') {
    http_response_code(403);

    echo json_encode([
        'success' => false,
        'message' => 'Forbidden'
    ]);

    exit;
}

$db = getDatabaseConnection();

$query = trim($_GET['q'] ?? '');

$users = Users::searchUsers($db,$query);

echo json_encode([
    'success' => true,
    'users' => $users
]);

exit;