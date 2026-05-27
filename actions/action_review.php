<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
$session = new Session();

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/workoutclass.class.php');
require_once(__DIR__ . '/../database/enrollments.class.php');

if (!$session->isLoggedIn()) {
    http_response_code(401);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$db = getDatabaseConnection();

$userId = $session->getId();

$classId = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT);
$rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
$review = trim($_POST['review'] ?? '');

if ($userId === null || $classId === false || $classId === null) {
    http_response_code(400);
    exit;
}

if ($rating === false || $rating === null || $rating < 1 || $rating > 5) {
    http_response_code(400);
    exit;
}

try {
    Enrollments::updateReview($db, (int)$userId, (int)$classId, (int)$rating, $review);

    http_response_code(200);
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    exit;
}
?>