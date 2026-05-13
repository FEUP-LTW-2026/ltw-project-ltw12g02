<?php

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../template/common.tpl.php');
require_once(__DIR__ . '/../template/auth.tpl.php');

$session = new Session();

generateHead('PowerPit - Login');
generateHeader();
$messages = $session->getMessages();
drawMessages($messages);
drawLoginForm($session->getMessages());

generateFooter();
?>