<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../utils/csrf.php');

$session = new Session();

evaluateCSRF($_POST['token'] ?? '');

$session->logout();

header('Location: ../pages/index.php');
exit;
?>