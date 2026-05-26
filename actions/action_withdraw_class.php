<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/workoutclass.class.php');
require_once(__DIR__ . '/../database/enrollments.class.php');

$session = new Session();

function isAjaxRequest(): bool {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function sendJsonResponse(int $statusCode, string $message): void {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode(['message' => $message]);
    exit;
}

if (!$session->isLoggedIn()) {
    if (isAjaxRequest()) {
        sendJsonResponse(401, 'You need to be logged in to withdraw from a class.');
    }

    header('Location: ../pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if (isAjaxRequest()) {
        sendJsonResponse(405, 'Invalid request method.');
    }

    header('Location: ../pages/profile.php');
    exit;
}

$db = getDatabaseConnection();

$userId = $session->getId();

if ($userId === null) {
    if (isAjaxRequest()) {
        sendJsonResponse(401, 'Invalid session.');
    }

    $session->logout();
    header('Location: ../pages/login.php');
    exit;
}

$classId = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT);

if ($classId === false || $classId === null) {
    sendJsonResponse(400, 'Invalid class.');
}

try {
    if (Enrollments::removeEnrollmentFromDb($db, $userId, $classId)) {
        sendJsonResponse(200, 'Class withdrawn successfully.');
    }
    else {
        sendJsonResponse(409, 'You have already withdraw from this class.');
    }
} catch (PDOException $e) {
    sendJsonResponse(500, 'Could not withdraw from the class.');
}