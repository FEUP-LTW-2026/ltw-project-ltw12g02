<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/workoutclass.class.php');
require_once(__DIR__ . '/../database/workoutclasstype.class.php');
require_once(__DIR__ . '/../database/trainers.class.php');
require_once(__DIR__ . '/../template/common.tpl.php');
require_once(__DIR__ . '/../template/admin_classes.tpl.php');

$session = new Session();

if (!$session->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$db = getDatabaseConnection();

$user = Users::getUser($db, $session->getId());

if ($user === null || $user->getRole() !== 'admin') {
    header('Location: index.php');
    exit;
}

$filter = $_GET['filter'] ?? 'all';

if ($filter === 'upcoming') {
    $classes = WorkoutClass::getUpcomingWorkoutClasses($db);
} else if ($filter === 'past') {
    $classes = WorkoutClass::getPastWorkoutClasses($db);
} else {
    $classes = WorkoutClass::getAllWorkoutClasses($db);
    $filter = 'all';
}

$classTypes = WorkoutClassType::getAllWorkoutClassTypes($db);
$trainers = Trainers::getAllTrainers($db);

generateHead('PowerPit - Manage Classes');
generateHeader($session);

drawMessages($session->getMessages());
drawAdminClassesPage($classes, $classTypes, $trainers, $filter, $db);

generateFooter();
?>