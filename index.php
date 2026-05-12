<?php 
require_once(__DIR__ . '/template/common.tpl.php');
require_once(__DIR__ . '/template/class.tpl.php');
require_once(__DIR__ . '/database/connection.db.php');
require_once(__DIR__ . '/database/workoutclasstype.class.php');

generateHead();
generateHeader();

$id = 2;

$db = getDatabaseConnection();

$workoutClassType = WorkoutClassType::getWorkoutClassType($db, $id);


drawClassHeader($workoutClassType);


generateFooter();
?>