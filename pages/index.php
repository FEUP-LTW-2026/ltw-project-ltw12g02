<?php
require_once(__DIR__ . '/../template/common.tpl.php');
require_once(__DIR__ . '/../template/home.tpl.php');

generateHead('PowerPit');
generateHeader();

drawHomepage();

generateFooter();
?>