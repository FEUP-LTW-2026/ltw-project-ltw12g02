<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../utils/csrf.php');
$session = new Session();

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');

if (!$session->isLoggedIn()) {
    http_response_code(401);
    header('Location: ../pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Location: ../pages/plans.php');
    exit;
}

evaluateCSRF($_POST['token'] ?? '');

$db = getDatabaseConnection();

$userId = $session->getId();

$plan= trim($_POST['membership'] ?? '');

if ($userId === null || $plan === false) {
    http_response_code(400);
    header('Location: ../pages/login.php');
    exit;
}

try {
    $user = Users::getUser($db, $userId);

    $user->changePlan($plan, $db);

    http_response_code(200);
    header('Location: ../pages/profile.php');
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    header('Location: ../pages/plans.php');
    exit;
}
?>