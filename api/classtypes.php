<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/api.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/workoutclasstype.class.php');
require_once(__DIR__ . '/../utils/api_serializer.php');

$db = getDatabaseConnection();

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') {
    header('Allow: GET');

    sendJson([
        'success' => false,
        'message' => 'Method not allowed.'
    ], 405);
}

$hasId = isset($_GET['id']);
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($hasId && $id === false) {
    sendJson([
        'success' => false,
        'message' => 'Invalid class type id.'
    ], 400);
}

if ($id !== null && $id !== false) {
    $type = WorkoutClassType::getWorkoutClassType($db, $id);

    if ($type === null) {
        sendJson([
            'success' => false,
            'message' => 'Class type not found.'
        ], 404);
    }

    sendJson(workoutClassTypeToJson($type));
}

$types = WorkoutClassType::getAllWorkoutClassTypes($db);

sendJson(workoutClassTypesToJson($types));
?>