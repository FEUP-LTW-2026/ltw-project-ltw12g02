<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/equipment.class.php');
require_once(__DIR__ . '/../template/common.tpl.php');
require_once(__DIR__ . '/../template/admin_equipment.tpl.php');

$session = new Session();

if (!$session->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$db = getDatabaseConnection();

$user = Users::getUser($db, (int)$session->getId());

if ($user === null || $user->getRole() !== 'admin') {
    header('Location: index.php');
    exit;
}

$equipment = Equipment::getAllEquipment($db);

generateHead('PowerPIT - Admin Equipment');
generateHeader($session);

drawMessages($session->getMessages());
drawAdminEquipmentPage($equipment);

generateFooter();
?>