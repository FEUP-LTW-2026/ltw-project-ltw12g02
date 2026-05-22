<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../template/common.tpl.php');
require_once(__DIR__ . '/../template/admin_trainers.tpl.php');

$session = new Session();

if (!$session->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$db = getDatabaseConnection();

$user = Users::getUser($db, $session->getId());

if ($user === null || $user->getRole() !== 'admin') {
    header('Location: index.php');
    exit;
}

$stmt = $db->prepare('
    SELECT 
        Trainers.TrainerId,
        Trainers.Bio,
        Trainers.Specializations,
        Trainers.Certifications,
        Users.UserId,
        Users.Name,
        Users.Username,
        Users.Email,
        Users.ProfileImage
    FROM Trainers
    JOIN Users ON Users.UserId = Trainers.UserId
    ORDER BY Users.Name ASC
');

$stmt->execute();

$trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);

generateHead('PowerPit - Manage Trainers');
generateHeader($session);

drawAdminTrainersPage($trainers);

generateFooter();
?>