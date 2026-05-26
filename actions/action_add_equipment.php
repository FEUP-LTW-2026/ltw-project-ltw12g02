<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/equipment.class.php');

$session = new Session();

if (!$session->isLoggedIn()) {
    header('Location: ../pages/login.php');
    exit;
}

$db = getDatabaseConnection();

$user = Users::getUser($db, (int)$session->getId());

if ($user === null || $user->getRole() !== 'admin') {
    header('Location: ../pages/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/admin_equipment.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$type = trim($_POST['type'] ?? '');
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
$status = $_POST['status'] ?? '';

if ($quantity === false || $quantity === null) {
    $session->addMessage('error', 'Invalid equipment quantity.');
    header('Location: ../pages/admin_equipment.php');
    exit;
}

try {
    $db->beginTransaction();

    $equipmentId = Equipment::addEquipment(
        $db,
        $name,
        $type,
        $quantity,
        $status
    );

    if (
        isset($_FILES['photo']) &&
        $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE
    ) {
        if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Photo upload failed.');
        }

        $temporaryPath = $_FILES['photo']['tmp_name'];

        if (!is_uploaded_file($temporaryPath)) {
            throw new Exception('Invalid uploaded file.');
        }

        $mimeType = mime_content_type($temporaryPath);

        if ($mimeType !== 'image/png') {
            throw new Exception('The equipment photo must be a PNG image.');
        }

        $destinationDirectory = __DIR__ . '/../assets/equipment';

        if (!is_dir($destinationDirectory)) {
            mkdir($destinationDirectory, 0775, true);
        }

        $destinationPath = $destinationDirectory . '/equipment' . $equipmentId . '.png';

        if (!move_uploaded_file($temporaryPath, $destinationPath)) {
            throw new Exception('The equipment photo could not be saved.');
        }
    }

    $db->commit();

    $session->addMessage('success', 'Equipment added successfully.');
} catch (Exception $exception) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }

    $session->addMessage('error', $exception->getMessage());
}

header('Location: ../pages/admin_equipment.php');
exit;
?>