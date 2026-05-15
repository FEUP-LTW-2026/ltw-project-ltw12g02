<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../database/workoutclasstype.class.php');
require_once(__DIR__ . '/../database/workoutclass.class.php');

function drawClassPage(WorkoutClassType $workoutClassType, PDO $db): void { 
    $workoutClasses = $workoutClassType->getWorkoutClasses($db);
?>
    <main>
        <?php drawClassHeader($workoutClassType); ?>
        <?php drawClassAbout($workoutClassType); ?>
        <?php drawClassImage(); ?>
        <?php drawAvailableClassesIntro($workoutClassType); ?>
        <?php drawAvailableClasses($workoutClassType, $workoutClasses); ?>
    </main>
<?php } ?>


<?php
function drawClassHeader(WorkoutClassType $workoutClassType): void { ?>
    <section class="image-bg">
        <div id="class-header">
            <h1><?= htmlspecialchars($workoutClassType->getName()) ?></h1>

            <img 
                src="<?= htmlspecialchars($workoutClassType->getImagePath()) ?>" 
                alt="<?= htmlspecialchars($workoutClassType->getName()) ?>" 
                width="600" 
                height="300"
            >

            <a href="#available-classes" class="btn light">Schedule class</a>
        </div>
    </section>
<?php } ?>


<?php
function drawClassAbout(WorkoutClassType $workoutClassType): void { ?>
    <section class="flex-row light">
        <article class="flex-item main">
            <h2>About <?= htmlspecialchars($workoutClassType->getName()) ?></h2>

            <p>
                <?= htmlspecialchars($workoutClassType->getDescription()) ?>
            </p>

            <h2>Objectives</h2>

            <ul>
                <li>Improve physical condition</li>
                <li>Increase strength and resistance</li>
                <li>Train with motivation and consistency</li>
            </ul>
        </article>

        <aside class="flex-item side">
            <div id="info" class="card">
                <h2 class="card-title center">Class Info</h2>

                <dl>
                    <div class="card-dl-row">
                        <dt>Duration</dt>
                        <dd><?= htmlspecialchars((string)$workoutClassType->getDuration()) ?> minutes</dd>
                    </div>

                    <div class="card-dl-row">
                        <dt>Type of training</dt>
                        <dd><?= htmlspecialchars($workoutClassType->getName()) ?></dd>
                    </div>

                    <div class="card-dl-row">
                        <dt>Intensity</dt>
                        <dd>Medium</dd>
                    </div>
                </dl>
            </div>
        </aside>
    </section>
<?php } ?>


<?php
function drawClassImage(): void { ?>
    <section class="flex-row dark">
        <div class="flex-item">
            <img 
                src="https://picsum.photos/600/300" 
                alt="Exercise example" 
                height="300" 
                width="600"
            >
        </div>
    </section>
<?php } ?>


<?php
function drawAvailableClassesIntro(WorkoutClassType $workoutClassType): void { ?>
    <section class="flex-row light">
        <div class="flex-item">
            <header>
                <h2>Available <?= htmlspecialchars($workoutClassType->getName()) ?> Classes</h2>
                <p>Choose one of the upcoming <?= htmlspecialchars($workoutClassType->getName()) ?> sessions.</p>
            </header>
        </div>
    </section>
<?php } ?>


<?php
function drawAvailableClasses(WorkoutClassType $workoutClassType, array $workoutClasses): void { ?>
    <section id="available-classes" class="grid">
        <?php if (empty($workoutClasses)) { ?>
            <article class="card">
                <h3>No classes available</h3>
                <p>There are no upcoming sessions for this class yet.</p>
            </article>
        <?php } ?>

        <?php foreach ($workoutClasses as $workoutClass) { ?>
            <?php drawClassCard($workoutClass); ?>
            <?php drawBookingDialog($workoutClassType, $workoutClass); ?>
        <?php } ?>
    </section>
<?php } ?>


<?php
function drawClassCard(WorkoutClass $workoutClass): void {
    $timestamp = strtotime($workoutClass->getClassDateTime());

    $day = date('l', $timestamp);
    $date = date('d M Y', $timestamp);
    $time = date('H:i', $timestamp);

    $dialogId = 'booking-dialog-' . $workoutClass->getId();
?>
    <article class="card">
        <h3><?= htmlspecialchars($day) ?></h3>

        <div class="card-wrap">
            <p><strong>Date:</strong> <?= htmlspecialchars($date) ?></p>
            <p><strong>Time:</strong> <?= htmlspecialchars($time) ?></p>
            <p><strong>Trainer:</strong> Trainer #<?= htmlspecialchars((string)$workoutClass->getTrainerId()) ?></p>
            <p><strong>Capacity:</strong> <?= htmlspecialchars((string)$workoutClass->getCapacity()) ?></p>

            <button 
                type="button" 
                class="btn small light card-action"
                data-dialog-target="<?= htmlspecialchars($dialogId) ?>"
            >
                Book class
            </button>
        </div>
    </article>
<?php } ?>


<?php
function drawBookingDialog(WorkoutClassType $workoutClassType, WorkoutClass $workoutClass): void {
    $timestamp = strtotime($workoutClass->getClassDateTime());

    $day = date('l', $timestamp);
    $date = date('d M Y', $timestamp);
    $time = date('H:i', $timestamp);

    $dialogId = 'booking-dialog-' . $workoutClass->getId();
?>
    <dialog id="<?= htmlspecialchars($dialogId) ?>" class="popup-dialog">
        <section class="card popup-card">
            <button 
                type="button" 
                class="popup-close" 
                data-dialog-close
                aria-label="Close booking dialog"
            >
                &times;
            </button>

            <header class="popup-header">
                <p class="profile-member-card-label">PowerPIT Booking</p>
                <h1>Book Class</h1>
                <p>Check the details before confirming your booking.</p>
            </header>

            <dl>
                <div class="card-dl-row">
                    <dt>Class</dt>
                    <dd><?= htmlspecialchars($workoutClassType->getName()) ?></dd>
                </div>

                <div class="card-dl-row">
                    <dt>Day</dt>
                    <dd><?= htmlspecialchars($day) ?></dd>
                </div>

                <div class="card-dl-row">
                    <dt>Date</dt>
                    <dd><?= htmlspecialchars($date) ?></dd>
                </div>

                <div class="card-dl-row">
                    <dt>Time</dt>
                    <dd><?= htmlspecialchars($time) ?></dd>
                </div>

                <div class="card-dl-row">
                    <dt>Trainer</dt>
                    <dd>Trainer #<?= htmlspecialchars((string)$workoutClass->getTrainerId()) ?></dd>
                </div>

                <div class="card-dl-row">
                    <dt>Duration</dt>
                    <dd><?= htmlspecialchars((string)$workoutClassType->getDuration()) ?> minutes</dd>
                </div>

                <div class="card-dl-row">
                    <dt>Capacity</dt>
                    <dd><?= htmlspecialchars((string)$workoutClass->getCapacity()) ?></dd>
                </div>
            </dl>

            <form 
                class="popup-form" 
                action="../actions/action_book_class.php" 
                method="post"
            >
                <input 
                    type="hidden" 
                    name="class_id" 
                    value="<?= htmlspecialchars((string)$workoutClass->getId()) ?>"
                >

                <div class="popup-actions">
                    <button 
                        type="button" 
                        class="btn small"
                        data-dialog-close
                    >
                        Cancel
                    </button>

                    <button type="submit" class="btn small light">
                        Confirm booking
                    </button>
                </div>
            </form>
        </section>
    </dialog>
<?php } ?>