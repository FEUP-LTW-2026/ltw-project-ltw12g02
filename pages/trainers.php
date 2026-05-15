<?php
declare(strict_types = 1);

ini_set('display_errors', '1'); ini_set('display_startup_errors', '1'); error_reporting(E_ALL);

ini_set('display_errors', '1'); 
ini_set('display_startup_errors', '1'); 
error_reporting(E_ALL);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../template/common.tpl.php');
require_once(__DIR__ . '/../template/trainers.tpl.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/trainers.class.php');

$session = new Session();

$db = getDatabaseConnection();

$trainers = Trainers::getAllTrainers($db);

generateHead('PowerPIT - Trainers');
generateHeader($session);

drawMessages($session->getMessages());
drawTrainersPage($trainers, $db);

generateFooter();
?>