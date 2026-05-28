<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/complaints.class.php');

$session = new Session();

if (!$session->isLoggedIn()) {
    header('Location: ../pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/profile.php');
    exit;
}

$db = getDatabaseConnection();

$userId = $session->getId();

if ($userId === null) {
    $session->logout();
    header('Location: ../pages/login.php');
    exit;
}

$user = Users::getUser($db, (int)$userId);

if ($user === null || $user->getRole() !== 'admin') {
    $session->addMessage('error', 'Only admins can respond to complaints.');
    header('Location: ../pages/profile.php');
    exit;
}

$complaintId = filter_input(INPUT_POST, 'complaint_id', FILTER_VALIDATE_INT);
$response = trim($_POST['response'] ?? '');

if ($complaintId === false || $complaintId === null) {
    $session->addMessage('error', 'Invalid complaint.');
    header('Location: ../pages/profile.php');
    exit;
}

if ($response === '') {
    $session->addMessage('error', 'Response cannot be empty.');
    header('Location: ../pages/profile.php');
    exit;
}

$complaint = Complaints::getComplaint($db, $complaintId);

if ($complaint === null) {
    $session->addMessage('error', 'Complaint not found.');
    header('Location: ../pages/profile.php');
    exit;
}

if (trim($complaint->get_response()) !== '') {
    $session->addMessage('error', 'This complaint has already been answered.');
    header('Location: ../pages/profile.php');
    exit;
}

try {
    Complaints::updateResponse($db, $complaintId, $response);
    $session->addMessage('success', 'Complaint response sent.');
} catch (PDOException $e) {
    $session->addMessage('error', 'Could not respond to complaint.');
}

header('Location: ../pages/profile.php');
exit;
?>