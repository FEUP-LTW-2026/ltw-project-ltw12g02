<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../database/workoutclasstype.class.php');

function drawClassesPage(array $classTypes): void { ?>
    <main class="marketing-page classes_page">
        <?php
            $heroImage = !empty($classTypes) ? $classTypes[0]->getImagePath() : '../assets/class1.png';
            $secondImage = count($classTypes) > 3 ? $classTypes[3]->getImagePath() : $heroImage;
        ?>

        <section class="media-section feature-section flex-row dark classes_feature">
            <article class="flex-item main">
                <p class="classes_label">PowerPIT Classes</p>

                <h1>Train in group. Push your limits.</h1>

                <p>
                    Discover high-energy classes designed for strength, endurance,
                    mobility and motivation. Choose your workout and train with others.
                </p>

            </article>

            <aside class="flex-item side">
                <img
                    src="<?= htmlspecialchars($heroImage) ?>"
                    alt="PowerPIT group class"
                >
            </aside>
        </section>

        <section class="content-section classes_carousel_section" id="classes-carousel">
            <header>
                <p class="classes_label">Choose your workout</p>
                <h1>Our group classes</h1>
            </header>

            <section class="classes_carousel" aria-label="Workout classes carousel">
                <button class="carousel_btn" type="button" data-carousel-prev>
                    &#8249;
                </button>

                <div class="carousel_viewport">
                    <div class="carousel_track">
                        <?php foreach ($classTypes as $workoutClassType) { ?>
                            <article class="catalog-card class_card">
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
        </section>

        <section class="media-section feature-section flex-row dark classes_feature">
            <aside class="flex-item side">
                <img
                    src="<?= htmlspecialchars($secondImage) ?>"
                    alt="PowerPIT training session"
                >
            </aside>

            <article class="flex-item main">
                <p class="classes_label">For every level</p>

                <h1>Find the right class for your goal.</h1>

                <p>
                    Whether you want to burn calories, build strength, improve flexibility
                    or recover after intense training, PowerPIT has a class for you.
                </p>

            </article>
        </section>
    </main>
<?php } ?>