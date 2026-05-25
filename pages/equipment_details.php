<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../template/common.tpl.php');
require_once(__DIR__ . '/../template/equipment_details.tpl.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/equipment.class.php');
require_once(__DIR__ . '/../database/equipmentreservation.class.php');

$session = new Session();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id === false || $id === null) {
    header('Location: equipment.php');
    exit;
}

$db = getDatabaseConnection();

$equipment = Equipment::getEquipment($db, $id);

generateHead('PowerPIT - Equipment Details');
generateHeader($session);

drawMessages($session->getMessages());

if ($equipment === null) {
    drawEquipmentNotFoundPage();
} else {
    drawEquipmentDetailsPage($equipment, $session, $equipment->getQuantity());
}

generateFooter();
?>