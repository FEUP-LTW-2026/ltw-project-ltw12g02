<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../template/common.tpl.php');
require_once(__DIR__ . '/../template/admin_add_trainer.tpl.php');

$session = new Session();

if (!$session->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if ($session->getRole() !== 'admin') {
    header('Location: index.php');
    exit;
}

$db = getDatabaseConnection();

generateHead('PowerPIT - Add Trainer');
generateHeader($session);
$messages = $session->getMessages();
drawMessages($messages);

drawAdminAddTrainerPage();

generateFooter();
?>