<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/trainers.class.php');
require_once(__DIR__ . '/../database/workoutclass.class.php');
require_once(__DIR__ . '/../database/workoutclasstype.class.php');

$session = new Session();
$db = getDatabaseConnection();

/* ADMIN FILTER */

if (($_GET['admin'] ?? '') === '1') {
    require_once(__DIR__ . '/../template/admin_classes.tpl.php');

    if (!$session->isLoggedIn()) {
        http_response_code(403);
        exit;
    }

    $user = Users::getUser($db, $session->getId());

    if ($user === null || $user->getRole() !== 'admin') {
        http_response_code(403);
        exit;
    }

    $filter = $_GET['filter'] ?? 'all';

    if ($filter === 'upcoming') {
        $classes = WorkoutClass::getUpcomingWorkoutClasses($db);
    } else if ($filter === 'past') {
        $classes = WorkoutClass::getPastWorkoutClasses($db);
    } else {
        $classes = WorkoutClass::getAllWorkoutClasses($db);
    }

    $trainerId = filter_input(INPUT_GET, 'trainer', FILTER_VALIDATE_INT);
    $date = trim($_GET['date'] ?? '');
    $time = trim($_GET['time'] ?? '');

    $filteredClasses = [];

    foreach ($classes as $class) {
        $timestamp = strtotime($class->getClassDateTime());

        if ($timestamp === false) {
            continue;
        }

        $classDate = date('Y-m-d', $timestamp);
        $classTime = date('H:i', $timestamp);

        if ($trainerId !== false && $trainerId !== null && $class->getTrainerId() !== $trainerId) {
            continue;
        }

        if ($date !== '' && $classDate !== $date) {
            continue;
        }

        if ($time !== '' && $classTime !== $time) {
            continue;
        }

        $filteredClasses[] = $class;
    }

    if (empty($filteredClasses)) { ?>
        <p class="empty-search-message">No classes found.</p>
    <?php
        exit;
    }

    foreach ($filteredClasses as $class) {
        drawAdminClassRow($class, $db);
    }

    exit;
}

/* PUBLIC CLASS PAGE FILTER */

require_once(__DIR__ . '/../template/class.tpl.php');

$classTypeId = $_GET['id'] ?? null;

if ($classTypeId === null) {
    http_response_code(400);
    exit;
}

$trainerId = $_GET['trainer'] ?? '';
$date = $_GET['date'] ?? '';
$time = $_GET['time'] ?? '';

$classes = WorkoutClass::getFilteredClasses(
    $db,
    $classTypeId,
    $trainerId,
    $date,
    $time
);

if (count($classes) === 0) { ?>
    <article class="card">
        <h3>No classes available</h3>
        <p>No sessions for this class found. Try changing your filters.</p>
    </article>
<?php
    return;
}

$workoutClassType = WorkoutClassType::getWorkoutClassType($db, (int) $classTypeId);

foreach ($classes as $workoutClass) { ?>
    <?php drawClassCard($db, $workoutClass); ?>

    <?php if ($workoutClassType !== null) { ?>
        <?php drawBookingDialog($db, $workoutClassType, $workoutClass); ?>
    <?php } ?>
<?php } ?>