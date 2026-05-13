<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../database/workoutclasstype.class.php');

function drawClassesPage(array $classTypes): void { ?>
    <main class="classes_page">
        <section class="classes_intro">
            <h1>Our Group Classes</h1>
            <p>Find the perfect class for your goals</p>
        </section>

        <section class="classes_list">
            <?php foreach ($classTypes as $workoutClassType) { ?>
                <article class="class_card">
                    <a href="class.php?id=<?= $workoutClassType->getId() ?>">
                        <img
                            src="<?= htmlspecialchars($workoutClassType->getImagePath()) ?>"
                            alt="<?= htmlspecialchars($workoutClassType->getName()) ?>"
                        >

                        <h2><?= htmlspecialchars($workoutClassType->getName()) ?></h2>
                    </a>
                </article>
            <?php } ?>
        </section>
    </main>
<?php } ?>