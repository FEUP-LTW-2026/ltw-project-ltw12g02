<?php 
require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../template/common.tpl.php');
require_once(__DIR__ . '/../template/profile.tpl.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');

$session = new Session();

if (!$session->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$db = getDatabaseConnection();

$user = Users::getUser($db, $session->getId());

if ($user === null) {
    $session->logout();
    header('Location: login.php');
    exit;
}

$workoutClasses = $user->getWorkoutClasses($db);
$messages = $session->getMessages();

generateHead('PowerPIT - Profile');
generateHeader($session);

drawMessages($messages);
drawProfile($user, $workoutClasses);

generateFooter();
?>