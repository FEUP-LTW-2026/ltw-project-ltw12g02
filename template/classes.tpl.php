<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../database/workoutclasstype.class.php');

function drawClassesPage(array $classTypes): void { ?>
    <main class="classes_page">
        <?php
            $heroImage = !empty($classTypes) ? $classTypes[0]->getImagePath() : '../assets/class1.png';
        ?>

        <section class="flex-row dark classes_hero">
            <article class="flex-item main">
                <p class="classes_hero_label">PowerPIT Classes</p>

                <h1>Our Group Classes</h1>

                <span class="hero_line"></span>

                <p>
                    Discover intense, dynamic and motivating group workouts.
                    Find the class that fits your goal and train with real energy.
                </p>
            </article>

            <aside class="flex-item side">
                <img 
                    src="<?= htmlspecialchars($heroImage) ?>" 
                    alt="PowerPIT group classes"
                >
            </aside>
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

                                <h2><?= htmlspecialchars($workoutClassType->getName()) ?></h2>
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