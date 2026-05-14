<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../database/workoutclasstype.class.php');

function drawClassesPage(array $classTypes): void { ?>
    <main class="classes_page">
        <section class="classes_banner">
            <p>PowerPIT Classes</p>
            <h1>Choose your workout</h1>
        </section>

        <section class="classes_carousel" aria-label="Workout classes carousel">
            <button class="carousel_btn" type="button" data-carousel-prev>
                &#8249;
            </button>

            <div class="carousel_viewport">
                <div class="carousel_track">
                    <?php foreach ($classTypes as $workoutClassType) { ?>
                        <article class="class_card">
                            <a href="class.php?id=<?= $workoutClassType->getId() ?>">
                                <img
                                    src="<?= htmlspecialchars($workoutClassType->getImagePath()) ?>"
                                    alt="<?= htmlspecialchars($workoutClassType->getName()) ?>"
                                >

                                <div class="class_card_info">
                                    <h2><?= htmlspecialchars($workoutClassType->getName()) ?></h2>
                                    <p><?= $workoutClassType->getDuration() ?> min</p>
                                </div>
                            </a>
                        </article>
                    <?php } ?>
                </div>
            </div>

            <button class="carousel_btn" type="button" data-carousel-next>
                &#8250;
            </button>
        </section>
    </main>
<?php } ?>