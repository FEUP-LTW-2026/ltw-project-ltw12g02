<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../template/common.tpl.php');
require_once(__DIR__ . '/../template/classes.tpl.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/workoutclasstype.class.php');

$session = new Session();

$db = getDatabaseConnection();

$classTypes = WorkoutClassType::getAllWorkoutClassTypes($db);

generateHead('PowerPIT - Classes');
generateHeader();

drawMessages($session->getMessages());
drawClassesPage($classTypes);

generateFooter();
?>