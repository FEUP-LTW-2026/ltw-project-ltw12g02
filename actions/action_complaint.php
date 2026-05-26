<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
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

$db = getDatabaseConnection();

$userId = $session->getId();

if ($userId === null) {
    $session->addMessage('error', 'Invalid session.');
    $session->logout();
    header('Location: ../pages/login.php');
    exit;
}

$reason = $_POST['reason'];
$details = $_POST['details'];

if ($reason === false || $reason === null || $details === false || $details === null) {
    $session->addMessage('error', 'Invalid field.');
    header('Location: ../pages/profile.php');
    exit;
}

try {
    Complaints::addComplaintToDb($db, $userId, $reason, $details);
    $session->addMessage('success', 'Complaint sent successfully.');
} catch (PDOException $e) {
    $session->addMessage('error', 'Could not send the complaint.');
}

header('Location: ../pages/profile.php');
exit;
?>