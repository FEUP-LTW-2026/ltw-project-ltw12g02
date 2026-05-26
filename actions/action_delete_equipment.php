<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/equipment.class.php');

$session = new Session();

if (!$session->isLoggedIn()) {
    header('Location: ../pages/login.php');
    exit;
}

$db = getDatabaseConnection();

$user = Users::getUser($db, (int)$session->getId());

if ($user === null || $user->getRole() !== 'admin') {
    header('Location: ../pages/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/admin_equipment.php');
    exit;
}

$equipmentId = filter_input(INPUT_POST, 'equipment_id', FILTER_VALIDATE_INT);

if ($equipmentId === false || $equipmentId === null) {
    $session->addMessage('error', 'Invalid equipment.');
    header('Location: ../pages/admin_equipment.php');
    exit;
}

try {
    Equipment::deleteEquipment($db, $equipmentId);

    $session->addMessage('success', 'Equipment removed successfully.');
} catch (Exception $exception) {
    $session->addMessage('error', $exception->getMessage());
}

header('Location: ../pages/admin_equipment.php');
exit;
?>