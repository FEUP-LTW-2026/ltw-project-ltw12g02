<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
$session = new Session();

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../template/class.tpl.php');
require_once(__DIR__ . '/../database/workoutclass.class.php');
require_once(__DIR__ . '/../database/workoutclasstype.class.php');


$db = getDatabaseConnection();

$classTypeId = $_GET['id'] ?? null;
if ($classTypeId === null) {
    http_response_code(400);
    exit;
}

$trainerId = $_GET['trainer'] ?? "";
$date = $_GET['date'] ?? "";
$time = $_GET['time'] ?? "";

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
<?php return; } 
foreach ($classes as $workoutClass) { ?>
    <?php drawClassCard($db,$workoutClass); ?>
    <?php drawBookingDialog($db, WorkoutClassType::getWorkoutClassType($db, (int)$classTypeId), $workoutClass); ?>
<?php } ?>

