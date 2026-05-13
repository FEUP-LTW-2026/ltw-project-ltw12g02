<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../database/workoutclasstype.class.php');

function drawClassPage(WorkoutClassType $workoutClassType): void { ?>
    <main>
        <?php drawClassHeader($workoutClassType); ?>
        <?php drawClassAbout($workoutClassType); ?>
        <?php drawClassImage(); ?>
        <?php drawAvailableClassesIntro($workoutClassType); ?>
        <?php drawAvailableClasses(); ?>
    </main>
<?php } ?>


<?php
function drawClassHeader(WorkoutClassType $workoutClassType): void { ?>
    <section class="image-bg">
        <div id="class-header">
            <h1><?= htmlspecialchars($workoutClassType->getName()) ?></h1>
            <img src="https://picsum.photos/600/300" alt="illustrative" width="600" height="300">
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
                <li>Fat loss</li>
                <li>Toning</li>
                <li>Better cardiovascular health</li>
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
                        <dd>Strength and core</dd>
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
            <img src="https://picsum.photos/600/300" alt="ExerciseExample" height="300" width="600">
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
function drawAvailableClasses(): void { ?>
    <section id="available-classes" class="grid">
        <?php drawClassCard('Monday', '09:00', 'Ana Costa', 'Studio A', 12); ?>
        <?php drawClassCard('Tuesday', '18:00', 'Miguel Silva', 'Studio B', 8); ?>
        <?php drawClassCard('Wednesday', '13:00', 'Sofia Martins', 'Studio A', 5); ?>
        <?php drawClassCard('Thursday', '19:30', 'Ricardo Lopes', 'Studio C', 10); ?>
        <?php drawClassCard('Saturday', '10:00', 'Ana Costa', 'Studio B', 0); ?>
    </section>
<?php } ?>


<?php
function drawClassCard(string $day, string $time, string $trainer, string $room, int $spots): void { ?>
    <article class="card <?= $spots === 0 ? 'full' : '' ?>">
        <h3><?= htmlspecialchars($day) ?></h3>

        <div class="card-wrap">
            <p><strong>Time:</strong> <?= htmlspecialchars($time) ?></p>
            <p><strong>Trainer:</strong> <?= htmlspecialchars($trainer) ?></p>
            <p><strong>Room:</strong> <?= htmlspecialchars($room) ?></p>
            <p><strong>Available spots:</strong> <?= $spots ?></p>

            <a 
                class="btn small light <?= $spots === 0 ? 'disabled' : '' ?> card-action" 
                href="booking.php"
            >
                Book class
            </a>
        </div>
    </article>
<?php } ?>