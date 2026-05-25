<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../utils/api.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../utils/api_serializer.php');

$session = new Session();

if (!$session->isLoggedIn()) {
    sendJson([
        'success' => false,
        'message' => 'You must be logged in.'
    ], 401);
}

$db = getDatabaseConnection();

$currentUser = Users::getUser($db, (int)$session->getId());

if ($currentUser === null) {
    sendJson([
        'success' => false,
        'message' => 'Invalid session.'
    ], 401);
}

$role = $currentUser->getRole();

if ($role !== 'admin') {
    sendJson([
        'success' => false,
        'message' => 'Only admins can access users.'
    ], 403);
}

$method = $_SERVER['REQUEST_METHOD'];

$hasId = isset($_GET['id']);
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($hasId && $id === false) {
    sendJson([
        'success' => false,
        'message' => 'Invalid user id.'
    ], 400);
}

$allowedRoles = ['member', 'trainer', 'admin'];

switch ($method) {
    case 'GET':
        if ($id !== null && $id !== false) {
            $user = Users::getUser($db, $id);

            if ($user === null) {
                sendJson([
                    'success' => false,
                    'message' => 'User not found.'
                ], 404);
            }

            sendJson(userToJson($user));
        }

        $users = Users::getAllUsers($db);

        sendJson(usersToJson($users));
        break;

    case 'POST':
        $input = getJsonInput();

        $name = trim((string)($input['name'] ?? ''));
        $username = trim((string)($input['username'] ?? ''));
        $email = trim((string)($input['email'] ?? ''));
        $password = (string)($input['password'] ?? '');
        $role = trim((string)($input['role'] ?? 'member'));

        if ($name === '' || $username === '' || $email === '' || $password === '') {
            sendJson([
                'success' => false,
                'message' => 'Name, username, email and password are required.'
            ], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendJson([
                'success' => false,
                'message' => 'Invalid email.'
            ], 400);
        }

        if (strlen($password) < 6) {
            sendJson([
                'success' => false,
                'message' => 'Password must have at least 6 characters.'
            ], 400);
        }

        if (!in_array($role, $allowedRoles, true)) {
            sendJson([
                'success' => false,
                'message' => 'Invalid role.'
            ], 400);
        }

        if (Users::usernameExists($db, $username)) {
            sendJson([
                'success' => false,
                'message' => 'Username already exists.'
            ], 409);
        }

        if (Users::emailExists($db, $email)) {
            sendJson([
                'success' => false,
                'message' => 'Email already exists.'
            ], 409);
        }

        $user = Users::create(
            $db,
            $name,
            $username,
            $email,
            $password
        );

        if ($user === null) {
            sendJson([
                'success' => false,
                'message' => 'Could not create user.'
            ], 500);
        }

        if ($role !== 'member') {
            $user->changeRole($role, $db);
        }

        sendJson([
            'success' => true,
            'message' => 'User created successfully.',
            'user' => userToJson($user)
        ], 201);

        break;

    case 'PATCH':
        if ($id === null || $id === false) {
            sendJson([
                'success' => false,
                'message' => 'User id is required.'
            ], 400);
        }

        $user = Users::getUser($db, $id);

        if ($user === null) {
            sendJson([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        $input = getJsonInput();

        $name = trim((string)($input['name'] ?? $user->getName()));
        $username = trim((string)($input['username'] ?? $user->getUserName()));
        $email = trim((string)($input['email'] ?? $user->getEmail()));
        $password = array_key_exists('password', $input) ? (string)$input['password'] : null;
        $newRole = array_key_exists('role', $input) ? trim((string)$input['role']) : $user->getRole();

        if ($name === '' || $username === '' || $email === '') {
            sendJson([
                'success' => false,
                'message' => 'Name, username and email cannot be empty.'
            ], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendJson([
                'success' => false,
                'message' => 'Invalid email.'
            ], 400);
        }

        if ($password !== null && $password !== '' && strlen($password) < 6) {
            sendJson([
                'success' => false,
                'message' => 'Password must have at least 6 characters.'
            ], 400);
        }

        if (!in_array($newRole, $allowedRoles, true)) {
            sendJson([
                'success' => false,
                'message' => 'Invalid role.'
            ], 400);
        }

        if (Users::usernameExistsForOtherUser($db, $username, $id)) {
            sendJson([
                'success' => false,
                'message' => 'Username already exists.'
            ], 409);
        }

        if ($user->getUserId() === $session->getId() && $newRole !== 'admin') {
            sendJson([
                'success' => false,
                'message' => 'You cannot remove your own admin role.'
            ], 400);
        }

        $user->updateUser(
            $db,
            $name,
            $username,
            $email,
            $password,
            $newRole
        );

        sendJson([
            'success' => true,
            'message' => 'User updated successfully.',
            'user' => userToJson($user)
        ]);

        break;

    case 'DELETE':
        if ($id === null || $id === false) {
            sendJson([
                'success' => false,
                'message' => 'User id is required.'
            ], 400);
        }

        $user = Users::getUser($db, $id);

        if ($user === null) {
            sendJson([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        if ($user->getUserId() === $session->getId()) {
            sendJson([
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ], 400);
        }

        $user->deleteUser($db);

        sendJson([
            'success' => true,
            'message' => 'User deleted successfully.'
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