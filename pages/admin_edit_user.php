<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../template/common.tpl.php');
require_once(__DIR__ . '/../template/admin_trainers.tpl.php');
require_once(__DIR__ . '/../template/admin_edit_users.tpl.php');

$session = new Session();

if (!$session->isLoggedIn() || $session->getRole() !== 'admin') {
    header('Location: login.php');
    exit;
}

$userid = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($userid === false || $userid === null) {
    header('Location: admin.php');
    exit;
}


$db = getDatabaseConnection();

$user = Users::getUser($db,$userid);

generateHead('PowerPit - Edit user');

generateHeader($session);

drawAdminEditUserPage($user);

generateFooter();


