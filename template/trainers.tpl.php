<?php
declare(strict_types = 1);

function drawTrainersPage(array $trainers, PDO $db): void { ?>
    <main class="marketing-page trainers_page">
        <?php
            $heroImage = '../assets/users/default.png';

            if (!empty($trainers)) {
                $firstUser = $trainers[0]->getUser($db);

                if ($firstUser !== null) {
                    $heroImage = '../assets/users/' . $firstUser->getProfileImage();
                }
            }
        ?>

        <section class="media-section feature-section flex-row dark classes_feature">
            <article class="flex-item main">
                <p class="classes_label">PowerPIT Trainers</p>

                <h1>
                    Train smarter<br>
                    Reach your goals
                </h1>

                <p>
                    Meet the certified trainers who will guide your workouts,
                    correct your technique and help you progress with confidence.
                </p>
            </article>

            <aside class="flex-item side">
                <img
                    src="<?= htmlspecialchars($heroImage) ?>"
                    alt="PowerPIT trainer"
                >
            </aside>
        </section>

        <section class="content-section classes_carousel_section">
            <header>
                <p class="classes_label">Meet the team</p>
                <h1>Our trainers</h1>
            </header>

            <?php if (empty($trainers)) { ?>
                <article class="card trainers_empty">
                    <h2>No trainers available</h2>
                    <p>There are no trainers registered at the moment.</p>
                </article>
            <?php } else { ?>
                <div class="grid trainers_grid">
                    <?php foreach ($trainers as $trainer) {
                        drawTrainerCard($trainer, $db);
                    } ?>
                </div>
            <?php } ?>
        </section>
    </main>
<?php }

function drawTrainerCard(Trainers $trainer, PDO $db): void {
    $user = $trainer->getUser($db);

    if ($user === null) {
        return;
    }

    $specializations = [];

    if ($trainer->getSpecializations() !== null && $trainer->getSpecializations() !== '') {
        $specializations = array_filter(array_map(
            'trim',
            explode(',', $trainer->getSpecializations())
        ));
    }
    ?>

    <article class="catalog-card card trainer_card">
        <a 
            href="../pages/trainer_profile.php?id=<?= htmlspecialchars((string)$trainer->getTrainerId()) ?>" 
            class="trainer_card_link"
        >
            <img 
                src="../assets/users/<?= htmlspecialchars($user->getProfileImage()) ?>" 
                alt="<?= htmlspecialchars($user->getName()) ?>"
            >

            <div class="trainer_card_content">
                <p class="profile-member-card-label">PowerPIT Coach</p>

                <h2><?= htmlspecialchars($user->getName()) ?></h2>

                <?php if ($trainer->getBio() !== null && $trainer->getBio() !== '') { ?>
                    <p class="trainer_bio">
                        <?= htmlspecialchars($trainer->getBio()) ?>
                    </p>
                <?php } ?>

                <?php if (!empty($specializations)) { ?>
                    <ul class="trainer_tags">
                        <?php foreach ($specializations as $specialization) { ?>
                            <li><?= htmlspecialchars($specialization) ?></li>
                        <?php } ?>
                    </ul>
                <?php } ?>

                <?php if ($trainer->getCertifications() !== null && $trainer->getCertifications() !== '') { ?>
                    <p class="trainer_certifications">
                        <strong>Certifications:</strong>
                        <?= htmlspecialchars($trainer->getCertifications()) ?>
                    </p>
                <?php } ?>
            </div>
        </a>
    </article>
<?php } ?>