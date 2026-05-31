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

if ($session->getRole() !== 'member') {
    $session->addMessage('error', 'Only members can reserve equipment.');
    header('Location: ../pages/equipment.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/equipment.php');
    exit;
}

evaluateCSRF($_POST['token'] ?? '');

$equipmentId = filter_input(INPUT_POST, 'equipment_id', FILTER_VALIDATE_INT);
$duration = filter_input(INPUT_POST, 'duration', FILTER_VALIDATE_INT);
$reservationDateTimeRaw = trim($_POST['reservation_datetime'] ?? '');

$redirect = '../pages/equipment.php';

if ($equipmentId !== false && $equipmentId !== null) {
    $redirect = '../pages/equipment_details.php?id=' . $equipmentId;
}

if (
    $equipmentId === false ||
    $equipmentId === null ||
    $duration === false ||
    $duration === null ||
    $reservationDateTimeRaw === ''
) {
    $session->addMessage('error', 'Please fill all reservation fields.');
    header('Location: ' . $redirect);
    exit;
}

try {
    $reservationDateTime = new DateTimeImmutable($reservationDateTimeRaw);
    $db = getDatabaseConnection();

    EquipmentReservation::createReservation(
        $db,
        (int)$session->getId(),
        $equipmentId,
        $reservationDateTime,
        $duration
    );

    $session->addMessage('success', 'Equipment reserved successfully.');
} catch (Exception $exception) {
    $session->addMessage('error', $exception->getMessage());
}

header('Location: ' . $redirect);
exit;
?>