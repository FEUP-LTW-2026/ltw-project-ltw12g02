<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/workoutclass.class.php');
require_once(__DIR__ . '/../database/enrollments.class.php');
require_once(__DIR__ . '/../template/class.tpl.php');

$session = new Session();

function isAjaxRequest(): bool {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function sendJsonResponse(int $statusCode, array $data): void {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

if (!$session->isLoggedIn()) {
    if (isAjaxRequest()) {
        sendJsonResponse(401, ['message' => 'You need to be logged in to book a class.']);
    }

    header('Location: ../pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if (isAjaxRequest()) {
        sendJsonResponse(405, ['message' => 'Invalid request method.']);
    }

    header('Location: ../pages/profile.php');
    exit;
}

$db = getDatabaseConnection();

$userId = $session->getId();

if ($userId === null) {
    if (isAjaxRequest()) {
        sendJsonResponse(401, ['message' => 'Invalid session.']);
    }

    $session->logout();
    header('Location: ../pages/login.php');
    exit;
}

$classId = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT);

if ($classId === false || $classId === null) {
    sendJsonResponse(400, ['message' =>'Invalid class.']);
}

try {
    Enrollments::addEnrollmentToDb($db, $userId, $classId);
    $class = WorkoutClass::getWorkoutClass($db, $classId);
    ob_start();
    drawClassCard($db, $class);
    $html = ob_get_clean();

    sendJsonResponse(200, ['message' => 'Class booked successfully.', 'html' => $html]);
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        sendJsonResponse(409, ['message' => 'You have already booked this class.']);
    }

    sendJsonResponse(500, ['message' => 'Could not complete the booking.']);
}