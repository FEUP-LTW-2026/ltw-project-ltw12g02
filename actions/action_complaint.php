<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../utils/csrf.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/complaints.class.php');

$session = new Session();

if (!$session->isLoggedIn()) {
    $session->addMessage('error', 'You need to be logged in to send a complaint.');
    header('Location: ../pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $session->addMessage('error', 'Invalid request method.');
    header('Location: ../pages/profile.php');
    exit;
}

evaluateCSRF($_POST['token'] ?? '');

$db = getDatabaseConnection();

$userId = $session->getId();

if ($userId === null) {
    $session->addMessage('error', 'Invalid session.');
    $session->logout();
    header('Location: ../pages/login.php');
    exit;
}

$user = Users::getUser($db, (int)$userId);

if ($user === null) {
    $session->addMessage('error', 'User not found.');
    $session->logout();
    header('Location: ../pages/login.php');
    exit;
}

$reason = trim($_POST['reason'] ?? '');
$details = trim($_POST['details'] ?? '');

$allowedReasons = [
    'equipment malfunction',
    'class cancellation',
    'other'
];

if (!in_array($reason, $allowedReasons, true)) {
    $session->addMessage('error', 'Invalid complaint reason.');
    header('Location: ../pages/profile.php');
    exit;
}

if ($details === '') {
    $session->addMessage('error', 'Complaint details cannot be empty.');
    header('Location: ../pages/profile.php');
    exit;
}

try {
    Complaints::addComplaintToDb($db, (int)$userId, $reason, $details);
    $session->addMessage('success', 'Complaint sent successfully.');
} catch (PDOException $e) {
    $session->addMessage('error', 'Could not send the complaint.');
}

header('Location: ../pages/profile.php');
exit;
?>