<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/trainers.class.php');

header('Content-Type: application/json');

$session = new Session();

if (!$session->isLoggedIn()) {
    http_response_code(403);

    echo json_encode([
        'success' => false,
        'message' => 'Forbidden'
    ]);

    exit;
}

$db = getDatabaseConnection();

$user = Users::getUser($db, $session->getId());

if ($user === null || $user->getRole() !== 'admin') {
    http_response_code(403);

    echo json_encode([
        'success' => false,
        'message' => 'Forbidden'
    ]);

    exit;
}

$query = trim($_GET['q'] ?? '');

$trainers = Trainers::searchTrainers($db, $query);

echo json_encode([
    'success' => true,
    'trainers' => $trainers
]);

exit;