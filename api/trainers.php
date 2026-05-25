<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../utils/api.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
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
        'message' => 'Invalid trainer id.'
    ], 400);
}

function checkTrainerAdmin(Session $session, PDO $db): void {
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
            'message' => 'Only admins can modify trainers.'
        ], 403);
    }
}

switch ($method) {
    case 'GET':
        if ($id !== null && $id !== false) {
            $trainer = Trainers::getTrainer($db, $id);

            if ($trainer === null) {
                sendJson([
                    'success' => false,
                    'message' => 'Trainer not found.'
                ], 404);
            }

            sendJson(trainerToJson($trainer, $db));
        }

        $trainers = Trainers::getAllTrainers($db);

        sendJson(trainersToJson($trainers, $db));
        break;

    case 'POST':
        checkTrainerAdmin($session, $db);

        $input = getJsonInput();

        $userId = filter_var($input['userId'] ?? null, FILTER_VALIDATE_INT);
        $bio = trim((string)($input['bio'] ?? ''));
        $specializations = trim((string)($input['specializations'] ?? ''));
        $certifications = trim((string)($input['certifications'] ?? ''));

        if ($userId === false || $userId === null) {
            sendJson([
                'success' => false,
                'message' => 'User id is required.'
            ], 400);
        }

        $user = Users::getUser($db, $userId);

        if ($user === null) {
            sendJson([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        if ($user->getRole() !== 'trainer') {
            $user->changeRole('trainer', $db);
        }

        $trainer = Trainers::getTrainerByUserId($db, $userId);

        if ($trainer === null) {
            sendJson([
                'success' => false,
                'message' => 'Trainer profile was not created.'
            ], 500);
        }

        $trainer->updateTrainer(
            $db,
            $bio,
            $specializations,
            $certifications
        );

        sendJson([
            'success' => true,
            'message' => 'Trainer created successfully.',
            'trainer' => trainerToJson($trainer, $db)
        ], 201);

        break;

    case 'PATCH':
        checkTrainerAdmin($session, $db);

        if ($id === null || $id === false) {
            sendJson([
                'success' => false,
                'message' => 'Trainer id is required.'
            ], 400);
        }

        $trainer = Trainers::getTrainer($db, $id);

        if ($trainer === null) {
            sendJson([
                'success' => false,
                'message' => 'Trainer not found.'
            ], 404);
        }

        $input = getJsonInput();

        $bio = trim((string)($input['bio'] ?? $trainer->getBio()));
        $specializations = trim((string)($input['specializations'] ?? $trainer->getSpecializations()));
        $certifications = trim((string)($input['certifications'] ?? $trainer->getCertifications()));

        $trainer->updateTrainer(
            $db,
            $bio,
            $specializations,
            $certifications
        );

        sendJson([
            'success' => true,
            'message' => 'Trainer updated successfully.',
            'trainer' => trainerToJson($trainer, $db)
        ]);

        break;

    case 'DELETE':
        checkTrainerAdmin($session, $db);

        if ($id === null || $id === false) {
            sendJson([
                'success' => false,
                'message' => 'Trainer id is required.'
            ], 400);
        }

        $trainer = Trainers::getTrainer($db, $id);

        if ($trainer === null) {
            sendJson([
                'success' => false,
                'message' => 'Trainer not found.'
            ], 404);
        }

        $user = Users::getUser($db, $trainer->getUserId());

        if ($user === null) {
            sendJson([
                'success' => false,
                'message' => 'Trainer user not found.'
            ], 404);
        }

        $user->changeRole('member', $db);

        sendJson([
            'success' => true,
            'message' => 'Trainer removed successfully. User is now a member.'
        ]);

        break;

    default:
        header('Allow: GET, POST, PATCH, DELETE');

        sendJson([
            'success' => false,
            'message' => 'Method not allowed.'
        ], 405);
}
?>