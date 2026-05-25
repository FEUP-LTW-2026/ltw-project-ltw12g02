<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../utils/api.php');

$session = new Session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson([
        'success' => false,
        'message' => 'Method not allowed.'
    ], 405);
}

$session->logout();

sendJson([
    'success' => true,
    'message' => 'Logged out successfully.'
]);