<?php
declare(strict_types = 1);


require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../template/common.tpl.php');
require_once(__DIR__ . '/../template/admin_users.tpl.php');

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

generateHead('PowerPit - Admin Dashboard');
generateHeader($session);


drawAdminSearchUserPage();

generateFooter();
?>