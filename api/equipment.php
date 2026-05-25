<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../utils/api.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/equipment.class.php');
require_once(__DIR__ . '/../utils/api_serializer.php');

$session = new Session();
$db = getDatabaseConnection();

$method = $_SERVER['REQUEST_METHOD'];

$hasId = isset($_GET['id']);
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($hasId && $id === false) {
    sendJson([
        'success' => false,
        'message' => 'Invalid equipment id.'
    ], 400);
}

function checkEquipmentAdmin(Session $session, PDO $db): void {
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
            'message' => 'Only admins can modify equipment.'
        ], 403);
    }
}

switch ($method) {
    case 'GET':
        if ($id !== null && $id !== false) {
            $equipment = Equipment::getEquipment($db, $id);

            if ($equipment === null) {
                sendJson([
                    'success' => false,
                    'message' => 'Equipment not found.'
                ], 404);
            }

            sendJson(equipmentToJson($equipment));
        }

        $equipmentList = Equipment::getAllEquipment($db);

        sendJson(equipmentListToJson($equipmentList));
        break;

    case 'POST':
        checkEquipmentAdmin($session, $db);

        $input = getJsonInput();

        $name = trim((string)($input['name'] ?? ''));
        $type = trim((string)($input['type'] ?? ''));
        $quantity = filter_var($input['quantity'] ?? null, FILTER_VALIDATE_INT);
        $status = trim((string)($input['status'] ?? 'available'));

        if ($name === '') {
            sendJson([
                'success' => false,
                'message' => 'Equipment name is required.'
            ], 400);
        }

        if ($type === '') {
            sendJson([
                'success' => false,
                'message' => 'Equipment type is required.'
            ], 400);
        }

        if ($quantity === false || $quantity === null || $quantity < 0) {
            sendJson([
                'success' => false,
                'message' => 'Invalid quantity.'
            ], 400);
        }

        if ($status === '') {
            sendJson([
                'success' => false,
                'message' => 'Availability status is required.'
            ], 400);
        }

        $equipment = Equipment::createEquipment(
            $db,
            $name,
            $type,
            $quantity,
            $status
        );

        if ($equipment === null) {
            sendJson([
                'success' => false,
                'message' => 'Could not create equipment.'
            ], 500);
        }

        sendJson([
            'success' => true,
            'message' => 'Equipment created successfully.',
            'equipment' => equipmentToJson($equipment)
        ], 201);

        break;

    case 'PATCH':
        checkEquipmentAdmin($session, $db);

        if ($id === null || $id === false) {
            sendJson([
                'success' => false,
                'message' => 'Equipment id is required.'
            ], 400);
        }

        $equipment = Equipment::getEquipment($db, $id);

        if ($equipment === null) {
            sendJson([
                'success' => false,
                'message' => 'Equipment not found.'
            ], 404);
        }

        $input = getJsonInput();

        $name = trim((string)($input['name'] ?? $equipment->getName()));
        $type = trim((string)($input['type'] ?? $equipment->getType()));

        $quantity = array_key_exists('quantity', $input)
            ? filter_var($input['quantity'], FILTER_VALIDATE_INT)
            : $equipment->getQuantity();

        $status = trim((string)($input['status'] ?? $equipment->getStatus()));

        if ($name === '') {
            sendJson([
                'success' => false,
                'message' => 'Equipment name cannot be empty.'
            ], 400);
        }

        if ($type === '') {
            sendJson([
                'success' => false,
                'message' => 'Equipment type cannot be empty.'
            ], 400);
        }

        if ($quantity === false || $quantity === null || $quantity < 0) {
            sendJson([
                'success' => false,
                'message' => 'Invalid quantity.'
            ], 400);
        }

        if ($status === '') {
            sendJson([
                'success' => false,
                'message' => 'Availability status cannot be empty.'
            ], 400);
        }

        $updated = $equipment->updateEquipment(
            $db,
            $name,
            $type,
            $quantity,
            $status
        );

        if (!$updated) {
            sendJson([
                'success' => false,
                'message' => 'Could not update equipment.'
            ], 500);
        }

        sendJson([
            'success' => true,
            'message' => 'Equipment updated successfully.',
            'equipment' => equipmentToJson($equipment)
        ]);

        break;

    case 'DELETE':
        checkEquipmentAdmin($session, $db);

        if ($id === null || $id === false) {
            sendJson([
                'success' => false,
                'message' => 'Equipment id is required.'
            ], 400);
        }

        try {
            $deleted = Equipment::deleteEquipment($db, $id);

            if (!$deleted) {
                sendJson([
                    'success' => false,
                    'message' => 'Equipment not found.'
                ], 404);
            }

            sendJson([
                'success' => true,
                'message' => 'Equipment deleted successfully.'
            ]);

        } catch (PDOException) {
            sendJson([
                'success' => false,
                'message' => 'Cannot delete this equipment because it has reservations.'
            ], 409);
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