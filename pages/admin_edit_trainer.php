<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/trainers.class.php');
require_once(__DIR__ . '/../template/common.tpl.php');
require_once(__DIR__ . '/../template/admin_edit_trainer.tpl.php');

$session = new Session();

if (!$session->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if ($session->getRole() !== 'admin') {
    header('Location: index.php');
    exit;
}

$trainerId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($trainerId === false || $trainerId === null) {
    header('Location: admin_trainers.php');
    exit;
}

$db = getDatabaseConnection();

$trainer = Trainers::getTrainer($db, $trainerId);

if ($trainer === null) {
    header('Location: admin_trainers.php');
    exit;
}

$user = $trainer->getUser($db);

if ($user === null) {
    header('Location: admin_trainers.php');
    exit;
}

generateHead('PowerPIT - Edit Trainer');
generateHeader($session);
$messages = $session->getMessages();
drawMessages($messages);

drawAdminEditTrainerPage($trainer, $user, $db);

generateFooter();
?>