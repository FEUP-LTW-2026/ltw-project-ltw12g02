<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');

header('Content-Type: application/json');

$session = new Session();

if (!$session->isLoggedIn() || $session->getRole() !== 'admin') {
    echo json_encode([
        'success' => false,
        'users' => []
    ]);
    exit;
}

$query = trim($_GET['q'] ?? '');

$db = getDatabaseConnection();

try {
    $users = Users::searchTrainerCandidates($db, $query);

    echo json_encode([
        'success' => true,
        'users' => $users
    ]);
    exit;

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'users' => []
    ]);
    exit;
}