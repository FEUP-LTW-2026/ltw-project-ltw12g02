<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../utils/api.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/workoutclass.class.php');
require_once(__DIR__ . '/../database/workoutclasstype.class.php');
require_once(__DIR__ . '/../database/trainers.class.php');
require_once(__DIR__ . '/../utils/api_serializer.php');

$session = new Session();
$db = getDatabaseConnection();

$method = $_SERVER['REQUEST_METHOD'];

$hasId = isset($_GET['id']);
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($hasId && $id === false) {
    sendJson([
        'success' => false,
        'message' => 'Invalid class id.'
    ], 400);
}

function checkClassesAdmin(Session $session, PDO $db): void {
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

    if ($currentUser->getRole() !== 'admin') {
        sendJson([
            'success' => false,
            'message' => 'Only admins can modify classes.'
        ], 403);
    }
}

switch ($method) {
    case 'GET':
        if ($id !== null && $id !== false) {
            $class = WorkoutClass::getWorkoutClass($db, $id);

            if ($class === null) {
                sendJson([
                    'success' => false,
                    'message' => 'Class not found.'
                ], 404);
            }

            sendJson(workoutClassToJson($class, $db));
        }

        $classes = WorkoutClass::getAllWorkoutClasses($db);

        sendJson(workoutClassesToJson($classes, $db));
        break;

    case 'POST':
        checkClassesAdmin($session, $db);

        $input = getJsonInput();

        $trainerId = filter_var($input['trainerId'] ?? null, FILTER_VALIDATE_INT);
        $classTypeId = filter_var($input['classTypeId'] ?? null, FILTER_VALIDATE_INT);
        $classDateTime = trim((string)($input['classDateTime'] ?? ''));
        $capacity = filter_var($input['capacity'] ?? null, FILTER_VALIDATE_INT);

        if ($trainerId === false || $trainerId === null) {
            sendJson([
                'success' => false,
                'message' => 'Trainer id is required.'
            ], 400);
        }

        if ($classTypeId === false || $classTypeId === null) {
            sendJson([
                'success' => false,
                'message' => 'Class type id is required.'
            ], 400);
        }

        if ($classDateTime === '') {
            sendJson([
                'success' => false,
                'message' => 'Class date and time are required.'
            ], 400);
        }

        if ($capacity === false || $capacity === null) {
            sendJson([
                'success' => false,
                'message' => 'Capacity is required.'
            ], 400);
        }

        try {
            WorkoutClass::createClass(
                $db,
                $trainerId,
                $classTypeId,
                $classDateTime,
                $capacity
            );

            $createdId = (int)$db->lastInsertId();
            $class = WorkoutClass::getWorkoutClass($db, $createdId);

            sendJson([
                'success' => true,
                'message' => 'Class created successfully.',
                'class' => $class !== null ? workoutClassToJson($class, $db) : null
            ], 201);

        } catch (InvalidArgumentException $e) {
            sendJson([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }

        break;

    case 'PATCH':
        checkClassesAdmin($session, $db);

        if ($id === null || $id === false) {
            sendJson([
                'success' => false,
                'message' => 'Class id is required.'
            ], 400);
        }

        $class = WorkoutClass::getWorkoutClass($db, $id);

        if ($class === null) {
            sendJson([
                'success' => false,
                'message' => 'Class not found.'
            ], 404);
        }

        $input = getJsonInput();

        $trainerId = array_key_exists('trainerId', $input)
            ? filter_var($input['trainerId'], FILTER_VALIDATE_INT)
            : $class->getTrainerId();

        $classTypeId = array_key_exists('classTypeId', $input)
            ? filter_var($input['classTypeId'], FILTER_VALIDATE_INT)
            : $class->getClassTypeId();

        $classDateTime = array_key_exists('classDateTime', $input)
            ? trim((string)$input['classDateTime'])
            : $class->getClassDateTime();

        $capacity = array_key_exists('capacity', $input)
            ? filter_var($input['capacity'], FILTER_VALIDATE_INT)
            : $class->getCapacity();

        if ($trainerId === false || $trainerId === null) {
            sendJson([
                'success' => false,
                'message' => 'Invalid trainer id.'
            ], 400);
        }

        if ($classTypeId === false || $classTypeId === null) {
            sendJson([
                'success' => false,
                'message' => 'Invalid class type id.'
            ], 400);
        }

        if ($classDateTime === '') {
            sendJson([
                'success' => false,
                'message' => 'Class date and time cannot be empty.'
            ], 400);
        }

        if ($capacity === false || $capacity === null) {
            sendJson([
                'success' => false,
                'message' => 'Invalid capacity.'
            ], 400);
        }

        try {
            $class->updateClass(
                $db,
                $trainerId,
                $classTypeId,
                $classDateTime,
                $capacity
            );

            $updatedClass = WorkoutClass::getWorkoutClass($db, $id);

            sendJson([
                'success' => true,
                'message' => 'Class updated successfully.',
                'class' => $updatedClass !== null ? workoutClassToJson($updatedClass, $db) : null
            ]);

        } catch (InvalidArgumentException $e) {
            sendJson([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }

        break;

    case 'DELETE':
        checkClassesAdmin($session, $db);

        if ($id === null || $id === false) {
            sendJson([
                'success' => false,
                'message' => 'Class id is required.'
            ], 400);
        }

        try {
            WorkoutClass::deleteClass($db, $id);

            sendJson([
                'success' => true,
                'message' => 'Class deleted successfully.'
            ]);

        } catch (InvalidArgumentException $e) {
            sendJson([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }

        break;

    default:
        header('Allow: GET, POST, PATCH, DELETE');

        sendJson([
            'success' => false,
            'message' => 'Method not allowed.'
        ], 405);
}
?>