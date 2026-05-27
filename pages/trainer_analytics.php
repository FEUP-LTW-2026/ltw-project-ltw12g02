<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/trainers.class.php');
require_once(__DIR__ . '/../database/traineranalytics.class.php');
require_once(__DIR__ . '/../template/common.tpl.php');
require_once(__DIR__ . '/../template/trainer_analytics.tpl.php');

$session = new Session();

if (!$session->isLoggedIn() || $session->getRole() !== 'trainer') {
    header('Location: login.php');
    exit;
}

$db = getDatabaseConnection();

$userId = $session->getId();

if ($userId === null) {
    $session->logout();
    header('Location: login.php');
    exit;
}

$user = Users::getUser($db, (int)$userId);

if ($user === null || $user->getRole() !== 'trainer') {
    $session->logout();
    header('Location: login.php');
    exit;
}

$trainer = Trainers::getTrainerByUserId($db, $user->getUserId());

if ($trainer === null) {
    header('Location: profile.php');
    exit;
}

$filter = $_GET['filter'] ?? 'upcoming';

if (!in_array($filter, ['all', 'upcoming', 'past'], true)) {
    $filter = 'upcoming';
}

$overview = TrainerAnalytics::getOverview($db, $trainer->getTrainerId());
$classPerformance = TrainerAnalytics::getClassPerformance($db, $trainer->getTrainerId(), $filter);
$recentReviews = TrainerAnalytics::getRecentReviews($db, $trainer->getTrainerId(), 5);
$personalStats = TrainerAnalytics::getPersonalClassStats($db, $trainer->getTrainerId());
$insights = TrainerAnalytics::getEngagementInsights($db, $trainer->getTrainerId(), $filter);

generateHead('PowerPIT - Trainer Analytics');
generateHeader($session);

drawMessages($session->getMessages());

drawTrainerAnalyticsPage(
    $user,
    $overview,
    $classPerformance,
    $recentReviews,
    $personalStats,
    $insights,
    $filter
);

generateFooter();
?>