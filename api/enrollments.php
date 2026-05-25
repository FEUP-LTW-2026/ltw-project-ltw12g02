<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../utils/api.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/enrollments.class.php');
require_once(__DIR__ . '/../database/workoutclass.class.php');
require_once(__DIR__ . '/../utils/api_serializer.php');

$session = new Session();
$db = getDatabaseConnection();

if (!$session->isLoggedIn()) {
    sendJson([
        'success' => false,
        'message' => 'You must be logged in.'
    ], 401);
}

$currentUser = Users::getUser($db, (int)$session->getId());

if ($currentUser === null) {
    sendJson([
        'success' => false,
        'message' => 'Invalid session.'
    ], 401);
}

$isAdmin = $currentUser->getRole() === 'admin';

$method = $_SERVER['REQUEST_METHOD'];

$hasId = isset($_GET['id']);
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($hasId && $id === false) {
    sendJson([
        'success' => false,
        'message' => 'Invalid enrollment id.'
    ], 400);
}

$requestedUserId = filter_input(INPUT_GET, 'userId', FILTER_VALIDATE_INT);

if (isset($_GET['userId']) && $requestedUserId === false) {
    sendJson([
        'success' => false,
        'message' => 'Invalid user id.'
    ], 400);
}

$userId = $currentUser->getUserId();

if ($requestedUserId !== null) {
    if (!$isAdmin && $requestedUserId !== $currentUser->getUserId()) {
        sendJson([
            'success' => false,
            'message' => 'You cannot access another user enrollments.'
        ], 403);
    }

    $userId = $requestedUserId;
}

switch ($method) {
    case 'GET':
        $enrollments = Enrollments::getUserEnrollments($db, $userId);

        sendJson(enrollmentsToJson($enrollments, $db));
        break;

    case 'POST':
        $input = getJsonInput();

        $classId = filter_var($input['classId'] ?? null, FILTER_VALIDATE_INT);

        if ($classId === false || $classId === null) {
            sendJson([
                'success' => false,
                'message' => 'Class id is required.'
            ], 400);
        }

        $class = WorkoutClass::getWorkoutClass($db, $classId);

        if ($class === null) {
            sendJson([
                'success' => false,
                'message' => 'Class not found.'
            ], 404);
        }

        if (strtotime($class->getClassDateTime()) < time()) {
            sendJson([
                'success' => false,
                'message' => 'Cannot enroll in a past class.'
            ], 400);
        }

        if (WorkoutClass::isFull($db, $classId, $class->getCapacity())) {
            sendJson([
                'success' => false,
                'message' => 'Class is full.'
            ], 400);
        }

        if (Enrollments::userHasActiveEnrollment($db, $currentUser->getUserId(), $classId)) {
            sendJson([
                'success' => false,
                'message' => 'You are already enrolled in this class.'
            ], 409);
        }

        $enrollment = Enrollments::createEnrollment(
            $db,
            $currentUser->getUserId(),
            $classId
        );

        if ($enrollment === null) {
            sendJson([
                'success' => false,
                'message' => 'Could not create enrollment.'
            ], 500);
        }

        sendJson([
            'success' => true,
            'message' => 'Class booked successfully.',
            'enrollment' => enrollmentToJson($enrollment, $db)
        ], 201);

        break;

    case 'DELETE':
        if ($id === null || $id === false) {
            sendJson([
                'success' => false,
                'message' => 'Enrollment id is required.'
            ], 400);
        }

        $enrollment = Enrollments::getEnrollment($db, $id);

        if ($enrollment === null) {
            sendJson([
                'success' => false,
                'message' => 'Enrollment not found.'
            ], 404);
        }

        if (!$isAdmin && $enrollment->get_user_id() !== $currentUser->getUserId()) {
            sendJson([
                'success' => false,
                'message' => 'You cannot cancel another user enrollment.'
            ], 403);
        }

        $cancelled = $enrollment->cancelEnrollment($db);

        if (!$cancelled) {
            sendJson([
                'success' => false,
                'message' => 'Could not cancel enrollment.'
            ], 400);
        }

        sendJson([
            'success' => true,
            'message' => 'Enrollment cancelled successfully.'
        ]);

        break;

    default:
        header('Allow: GET, POST, DELETE');

        sendJson([
            'success' => false,
            'message' => 'Method not allowed.'
        ], 405);
}
?>