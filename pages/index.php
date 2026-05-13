<?php
require_once(__DIR__ . '/../template/common.tpl.php');
require_once(__DIR__ . '/../template/home.tpl.php');
require_once(__DIR__ . '/../utils/session.php');

$session = new Session();

generateHead('PowerPit');
generateHeader($session);

drawHomepage();

generateFooter();
?>