<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/trainers.class.php');
require_once(__DIR__ . '/../database/workoutclass.class.php');

require_once(__DIR__ . '/../database/workoutclasstype.class.php');
require_once(__DIR__ . '/admin_classes.tpl.php');

function drawAdminEditTrainerPage(Trainers $trainer, Users $user, PDO $db): void {
    $assignedClasses = $trainer->getAssignedClasses($db);
    $reviews = $trainer->getReviews($db);
    $ratings = $trainer->getAverageRatings($db);

    $classTypes = WorkoutClassType::getAllWorkoutClassTypes($db);
    $allTrainers = Trainers::getAllTrainers($db);
    ?>

    <main class="page-shell admin-users-page">

        <section class="hero-panel admin-users-hero">
            <p class="admin-label">PowerPIT Admin</p>

            <h1>Edit Trainer</h1>

            <p>
                Review this trainer profile, update trainer information and check their assigned classes.
            </p>
        </section>

        <section class="card admin-edit-user-card">

            <header class="admin-section-header">
                <div>
                    <p class="admin-label">Trainer Profile</p>
                    <h2><?= htmlspecialchars($user->getName()) ?></h2>
                </div>

                <p>
                    Trainer ID:
                    <strong><?= htmlspecialchars((string) $trainer->getTrainerId()) ?></strong>
                </p>
            </header>

            <div class="admin-edit-user-profile">
                <img
                    src="../assets/users/<?= htmlspecialchars($user->getProfileImage()) ?>"
                    alt="<?= htmlspecialchars($user->getName()) ?>"
                    class="admin-edit-user-avatar"
                >

                <div class="admin-edit-user-main-info">
                    <strong>
                        <?= htmlspecialchars($user->getName()) ?>
                    </strong>

                    <p>
                        @<?= htmlspecialchars($user->getUserName()) ?>
                    </p>

                    <p class="admin-user-email">
                        <?= htmlspecialchars($user->getEmail()) ?>
                    </p>
                </div>

                <span class="pill admin-user-role">
                    <?= htmlspecialchars($user->getRole()) ?>
                </span>
            </div>

            <dl>
                <div class="card-dl-row">
                    <dt>User ID</dt>
                    <dd><?= htmlspecialchars((string) $user->getUserId()) ?></dd>
                </div>

                <div class="card-dl-row">
                    <dt>Trainer ID</dt>
                    <dd><?= htmlspecialchars((string) $trainer->getTrainerId()) ?></dd>
                </div>

                <div class="card-dl-row">
                    <dt>Name</dt>
                    <dd><?= htmlspecialchars($user->getName()) ?></dd>
                </div>

                <div class="card-dl-row">
                    <dt>Username</dt>
                    <dd>@<?= htmlspecialchars($user->getUserName()) ?></dd>
                </div>

                <div class="card-dl-row">
                    <dt>Email</dt>
                    <dd><?= htmlspecialchars($user->getEmail()) ?></dd>
                </div>

                <div class="card-dl-row">
                    <dt>Role</dt>
                    <dd><?= htmlspecialchars($user->getRole()) ?></dd>
                </div>

                <div class="card-dl-row">
                    <dt>Average Rating</dt>
                    <dd>
                        <?= htmlspecialchars((string) ($ratings['AverageRating'] ?? 0)) ?>/5
                    </dd>
                </div>

                <div class="card-dl-row">
                    <dt>Total Reviews</dt>
                    <dd>
                        <?= htmlspecialchars((string) ($ratings['TotalReviews'] ?? 0)) ?>
                    </dd>
                </div>
            </dl>

        </section>

        <section class="card admin-edit-user-section">

            <header class="admin-section-header">
                <div>
                    <p class="admin-label">Edit Information</p>
                    <h2>Trainer Details</h2>
                </div>

                <p>
                    Update the trainer profile shown on the trainers page.
                </p>
            </header>

            <form
                action="../actions/action_admin_edit_trainer.php"
                method="post"
                class="form-stack powerpit_form"
            >
                <input
                    type="hidden"
                    name="trainer_id"
                    value="<?= htmlspecialchars((string) $trainer->getTrainerId()) ?>"
                >

                <label for="bio">
                    Bio

                    <textarea
                        id="bio"
                        name="bio"
                        required
                    ><?= htmlspecialchars($trainer->getBio() ?? '') ?></textarea>
                </label>

                <label for="specializations">
                    Specializations

                    <textarea
                        id="specializations"
                        name="specializations"
                        placeholder="Example: Strength, Mobility, Cardio"
                    ><?= htmlspecialchars($trainer->getSpecializations() ?? '') ?></textarea>
                </label>

                <label for="certifications">
                    Certifications

                    <textarea
                        id="certifications"
                        name="certifications"
                        placeholder="Example: Certified Personal Trainer"
                    ><?= htmlspecialchars($trainer->getCertifications() ?? '') ?></textarea>
                </label>

                <div class="actions-row popup-actions">
                    <a href="../pages/admin_trainers.php" class="btn">
                        Cancel
                    </a>

                    <button type="submit" class="btn light">
                        Save Changes
                    </button>
                </div>
            </form>

        </section>

        <section class="stats-grid admin-stats">
            <article class="stat-card admin-stat-card">
                <span>Assigned Classes</span>
                <strong><?= htmlspecialchars((string) count($assignedClasses)) ?></strong>
                <p>Classes currently connected to this trainer.</p>
            </article>

            <article class="stat-card admin-stat-card">
                <span>Average Rating</span>
                <strong><?= htmlspecialchars((string) ($ratings['AverageRating'] ?? 0)) ?></strong>
                <p>Average score from member reviews.</p>
            </article>

            <article class="stat-card admin-stat-card">
                <span>Total Reviews</span>
                <strong><?= htmlspecialchars((string) ($ratings['TotalReviews'] ?? 0)) ?></strong>
                <p>Reviews received from class enrollments.</p>
            </article>
        </section>

        <section class="card trainer_roster_card admin-edit-user-section">

            <header class="admin-section-header">
                <div>
                    <p class="admin-label">Schedule</p>
                    <h2>Assigned Classes</h2>
                </div>

                <p>
                    Classes where this trainer is assigned.
                </p>
            </header>

            <?php drawAdminEditTrainerClasses($assignedClasses); ?>

            <?php foreach ($assignedClasses as $class) { ?>
                <?php drawAdminClassFormDialog(
                    'admin-class-edit-dialog-' . $class->getId(),
                    'Edit Class',
                    'update',
                    $classTypes,
                    $allTrainers,
                    $db,
                    $class
                ); ?>
            <?php } ?>

        </section>

        <section class="card trainer_roster_card admin-edit-user-section">

            <header class="admin-section-header">
                <div>
                    <p class="admin-label">Feedback</p>
                    <h2>Reviews</h2>
                </div>

                <p>
                    Reviews left by members after classes.
                </p>
            </header>

            <?php drawAdminEditTrainerReviews($reviews); ?>

        </section>

        <section class="card admin-edit-user-section">

            <header class="admin-section-header">
                <div>
                    <p class="admin-label">Account Actions</p>
                    <h2>Manage Trainer</h2>
                </div>

                <p>
                    Go back to trainer management or edit the related user account.
                </p>
            </header>

            <div class="actions-row popup-actions">
                <a href="../pages/admin_trainers.php" class="btn">
                    Back to Trainers
                </a>

                <a
                    href="../pages/admin_edit_user.php?id=<?= htmlspecialchars((string) $user->getUserId()) ?>"
                    class="btn light"
                >
                    Edit User Account
                </a>
            </div>

        </section>

    </main>
<?php } ?>


<?php
function drawAdminEditTrainerClasses(array $classes): void { ?>
    <?php if (empty($classes)) { ?>
        <p class="empty-search-message">
            This trainer has no assigned classes.
        </p>
    <?php } else { ?>
        <ul class="avatar-list trainer_roster_members">
            <?php foreach ($classes as $class) { ?>
                <li>
                    <div class="icon-box admin-icon-box">
                        <i class="fa fa-calendar"></i>
                    </div>

                    <div>
                        <strong>
                            Class #<?= htmlspecialchars((string) $class->getId()) ?>
                        </strong>

                        <span>
                            Date: <?= htmlspecialchars($class->getClassDateTime()) ?>
                        </span>

                        <span>
                            Type ID: <?= htmlspecialchars((string) $class->getClassTypeId()) ?>
                        </span>
                    </div>

                    <span class="pill admin-user-role">
                        Capacity <?= htmlspecialchars((string) $class->getCapacity()) ?>
                    </span>

                    <div class="row-actions admin-user-actions">
                        <button
                            type="button"
                            data-dialog-target="admin-class-edit-dialog-<?= htmlspecialchars((string) $class->getId()) ?>"
                        >
                            Edit
                        </button>
                    </div>
                </li>
            <?php } ?>
        </ul>
    <?php } ?>
<?php } ?>


<?php
function drawAdminEditTrainerReviews(array $reviews): void { ?>
    <?php if (empty($reviews)) { ?>
        <p class="empty-search-message">
            This trainer has no reviews yet.
        </p>
    <?php } else { ?>
        <ul class="avatar-list trainer_roster_members">
            <?php foreach ($reviews as $review) { ?>
                <?php
                    $profileImage = $review['ProfileImage'] ?: 'default.png';
                    $reviewText = $review['Review'] ?: 'No written review.';
                ?>

                <li>
                    <img
                        src="../assets/users/<?= htmlspecialchars($profileImage) ?>"
                        alt="<?= htmlspecialchars($review['Name']) ?>"
                    >

                    <div>
                        <strong>
                            <?= htmlspecialchars($review['Name']) ?>
                        </strong>

                        <span>
                            @<?= htmlspecialchars($review['Username']) ?>
                            · <?= htmlspecialchars($review['ClassType']) ?>
                            · <?= htmlspecialchars($review['ClassDateTime']) ?>
                        </span>

                        <span>
                            <?= htmlspecialchars($reviewText) ?>
                        </span>
                    </div>

                    <span class="pill admin-user-role">
                        <?= htmlspecialchars((string) $review['Rating']) ?>/5
                    </span>
                </li>
            <?php } ?>
        </ul>
    <?php } ?>
<?php } ?>