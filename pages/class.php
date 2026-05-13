<?php 
require_once(__DIR__ . '/../template/common.tpl.php');
require_once(__DIR__ . '/../template/class.tpl.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/workoutclasstype.class.php');
require_once(__DIR__ . '/../utils/session.php');

$session = new Session();

$id = 2;

$db = getDatabaseConnection();

$workoutClassType = WorkoutClassType::getWorkoutClassType($db, $id);

generateHead('PowerPit - ' . $workoutClassType->getName());
generateHeader($session);
drawClassPage($workoutClassType);

generateFooter();
?>