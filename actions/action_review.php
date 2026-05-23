<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
$session = new Session();

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/workoutclass.class.php');
require_once(__DIR__ . '/../database/enrollments.class.php');

if (!$session->isLoggedIn()) {
    header('Location: ../pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/profile.php');
    exit;
}


$db = getDatabaseConnection();

$userId = $session->getId();
$classId = intval($_POST['class_id']);
$rating = intval($_POST['rating']);
$review = $_POST['review'];

if ($rating < 1 || $rating > 5) {
    $session->addMessage('error', 'Invalid rating!');
    header('Location: ../pages/profile.php');
    exit;
}

Enrollments::updateReview($db, $userId, $classId, $rating, $review);


