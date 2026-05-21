<?php
declare(strict_types = 1);


require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../template/common.tpl.php');
require_once(__DIR__ . '/../template/dashboard.tpl.php');

$session = new Session();

if (!$session->isLoggedIn()) {
    header('Location: index.php');
    exit;
}



$db = getDatabaseConnection();

$role = Users::getUser($db,$session->getId())->getRole();



if ($role !== 'admin') {
    header('Location: index.php');
    exit;
}
function getTableCount(PDO $db, string $table): int {
    $stmt = $db->query("SELECT COUNT(*) FROM $table");
    return (int) $stmt->fetchColumn();
}

$stats = [
    'users' => getTableCount($db, 'Users'),
    'trainers' => getTableCount($db, 'Trainers'),
    'classes' => getTableCount($db, 'Classes'),
    'equipment' => getTableCount($db, 'Equipment'),
    'enrollments' => getTableCount($db, 'Enrollments')
];

$adminName = $session->getUsername();

generateHead('PowerPit - Admin Dashboard');
generateHeader($session);

drawAdminDashboard($adminName, $stats);

generateFooter();
?>