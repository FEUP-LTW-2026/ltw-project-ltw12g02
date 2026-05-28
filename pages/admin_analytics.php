<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/admin_analytics.class.php');
require_once(__DIR__ . '/../template/common.tpl.php');
require_once(__DIR__ . '/../template/admin_analytics.tpl.php');

$session = new Session();

if (!$session->isLoggedIn()) {
    header('Location: index.php');
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

if ($user === null || $user->getRole() !== 'admin') {
    header('Location: index.php');
    exit;
}

$overview = AdminAnalytics::getOverview($db);
$popularClasses = AdminAnalytics::getPopularClasses($db, 6);
$equipmentUsage = AdminAnalytics::getEquipmentUsage($db, 6);
$recentReviews = AdminAnalytics::getRecentReviews($db, 5);

generateHead('PowerPIT - Admin Analytics');
generateHeader($session);

drawMessages($session->getMessages());

drawAdminAnalyticsPage(
    $user->getName(),
    $overview,
    $popularClasses,
    $equipmentUsage,
    $recentReviews
);

generateFooter();
?>