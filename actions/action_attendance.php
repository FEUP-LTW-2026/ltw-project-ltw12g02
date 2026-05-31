<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../utils/csrf.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/trainers.class.php');
require_once(__DIR__ . '/../database/workoutclass.class.php');
require_once(__DIR__ . '/../database/workoutclasstype.class.php');
require_once(__DIR__ . '/../template/class.tpl.php');

$session = new Session();
$db = getDatabaseConnection();

evaluateCSRF($_POST['token'] ?? '');

$userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
$classId = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT);
$attendance = (bool)$_POST['attendance'] ?? false;

if ($userId === null || $classId === null || $attendance === null) {
    http_response_code(400);
    exit;
}

try {
    Enrollments::updateAttendance($db, $userId, $classId, $attendance);
    http_response_code(200);
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    exit;
}
?>