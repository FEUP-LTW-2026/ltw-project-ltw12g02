<?php 
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../template/common.tpl.php');
require_once(__DIR__ . '/../template/profile.tpl.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/trainers.class.php');
require_once(__DIR__ . '/../database/enrollments.class.php');
require_once(__DIR__ . '/../database/workoutclass.class.php');
require_once(__DIR__ . '/../database/workoutclasstype.class.php');

$session = new Session();
$db = getDatabaseConnection();

$trainerId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($trainerId === false || $trainerId === null) {
    header('Location: trainers.php');
    exit;
}

$trainer = Trainers::getTrainer($db, $trainerId);

if ($trainer === null) {
    header('Location: trainers.php');
    exit;
}

$trainerUser = $trainer->getUser($db);

if ($trainerUser === null) {
    header('Location: trainers.php');
    exit;
}

$canEdit = $session->isLoggedIn() && $session->getId() === $trainerUser->getUserId();

$messages = $session->getMessages();

generateHead('PowerPIT - ' . $trainerUser->getName());
generateHeader($session);

drawMessages($messages);
drawTrainerProfile($db, $trainerUser, $trainer, $canEdit);

generateFooter();
?>