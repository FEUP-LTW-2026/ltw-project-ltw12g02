<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../utils/api.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/equipment.class.php');
require_once(__DIR__ . '/../database/equipmentreservation.class.php');
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
        'message' => 'Invalid reservation id.'
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
            'message' => 'You cannot access another user reservations.'
        ], 403);
    }

    $userId = $requestedUserId;
}

switch ($method) {
    case 'GET':
        $reservations = EquipmentReservation::getUserUpcomingReservations($db, $userId);

        sendJson(equipmentReservationsToJson($reservations));
        break;

    case 'POST':
        $input = getJsonInput();

        $equipmentId = filter_var($input['equipmentId'] ?? null, FILTER_VALIDATE_INT);
        $reservationDateTimeText = trim((string)($input['reservationDateTime'] ?? ''));
        $duration = filter_var($input['duration'] ?? null, FILTER_VALIDATE_INT);

        if ($equipmentId === false || $equipmentId === null) {
            sendJson([
                'success' => false,
                'message' => 'Equipment id is required.'
            ], 400);
        }

        if ($reservationDateTimeText === '') {
            sendJson([
                'success' => false,
                'message' => 'Reservation date and time are required.'
            ], 400);
        }

        if ($duration === false || $duration === null) {
            sendJson([
                'success' => false,
                'message' => 'Duration is required.'
            ], 400);
        }

        try {
            $reservationDateTime = new DateTimeImmutable($reservationDateTimeText);

            EquipmentReservation::createReservation(
                $db,
                $currentUser->getUserId(),
                $equipmentId,
                $reservationDateTime,
                $duration
            );

            sendJson([
                'success' => true,
                'message' => 'Equipment reservation created successfully.'
            ], 201);

        } catch (Exception $e) {
            sendJson([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }

        break;

    case 'DELETE':
        if ($id === null || $id === false) {
            sendJson([
                'success' => false,
                'message' => 'Reservation id is required.'
            ], 400);
        }

        try {
            EquipmentReservation::cancelReservation(
                $db,
                $id,
                $userId
            );

            sendJson([
                'success' => true,
                'message' => 'Equipment reservation cancelled successfully.'
            ]);

        } catch (Exception $e) {
            sendJson([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }

        break;

    default:
        header('Allow: GET, POST, DELETE');

        sendJson([
            'success' => false,
            'message' => 'Method not allowed.'
        ], 405);
}
?>