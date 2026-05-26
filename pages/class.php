<?php 
declare(strict_types = 1);

require_once(__DIR__ . '/../template/common.tpl.php');
require_once(__DIR__ . '/../template/class.tpl.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/workoutclasstype.class.php');
require_once(__DIR__ . '/../utils/session.php');

$session = new Session();

$db = getDatabaseConnection();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die('Invalid class type.');
}

$workoutClassType = WorkoutClassType::getWorkoutClassType($db, $id);

if ($workoutClassType === null) {
    die('Class type not found.');
}

generateHead('PowerPit - ' . $workoutClassType->getName());
generateHeader($session);
generateTemplates();

drawClassPage($workoutClassType, $db);

generateFooter();
?>