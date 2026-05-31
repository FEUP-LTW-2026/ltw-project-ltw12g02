<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../utils/csrf.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/equipmentreservation.class.php');

$session = new Session();

if (!$session->isLoggedIn()) {
    header('Location: ../pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/profile.php');
    exit;
}

evaluateCSRF($_POST['token'] ?? '');

$reservationId = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);

if ($reservationId === false || $reservationId === null) {
    $session->addMessage('error', 'Invalid equipment reservation.');
    header('Location: ../pages/profile.php');
    exit;
}

$db = getDatabaseConnection();

EquipmentReservation::cancelReservation(
    $db,
    $reservationId,
    (int)$session->getId()
);

$session->addMessage('success', 'Equipment reservation cancelled.');

header('Location: ../pages/profile.php');
exit;
?>