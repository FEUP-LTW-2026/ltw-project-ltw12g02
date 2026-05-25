<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/trainers.class.php');
require_once(__DIR__ . '/../database/enrollments.class.php');
require_once(__DIR__ . '/../database/workoutclass.class.php');
require_once(__DIR__ . '/../database/workoutclasstype.class.php');


function getWorkoutClassDisplayName(PDO $db, WorkoutClass $workoutClass): string {
    if (!method_exists($workoutClass, 'getClassTypeId')) {
        return 'Class #' . $workoutClass->getId();
    }

    $classType = WorkoutClassType::getWorkoutClassType($db, $workoutClass->getClassTypeId());

    if ($classType === null) {
        return 'Class #' . $workoutClass->getId();
    }

    return $classType->getName();
}


function splitTrainerText(?string $text): array {
    if ($text === null || trim($text) === '') {
        return [];
    }

    return array_filter(array_map('trim', explode(',', $text)));
}

function renderStars(int $rating): string {
    $html = '<span class="rating-display">';

    for ($i = 1; $i <= 5; $i++) {
        $filled = $i <= $rating ? 'fill' : '';
        $html .= "<span class='star $filled'>★</span>";
    }

    $html .= '</span>';

    return $html;
}


function drawEditProfileDialog(Users $user): void { ?>
    <dialog id="edit-profile-dialog" class="popup-dialog">
        <section class="card popup-card">
            <button 
                type="button" 
                class="popup-close" 
                data-dialog-close
                aria-label="Close edit profile dialog"
            >
                &times;
            </button>

            <header class="popup-header">
                <p class="profile-member-card-label">PowerPIT Account</p>
                <h1>Edit Profile</h1>
                <p>Update your profile information</p>
            </header>

            <form 
                class="popup-form"
                id="edit-profile-form
                action="../actions/action_edit_profile.php" 
                method="post"
                enctype="multipart/form-data"
            >
                <label class="popup-photo" for="profile-image-input">
                    <img 
                        class="profile-image-preview"
                        src="../assets/users/<?= htmlspecialchars($user->getProfileImage()) ?>" 
                        alt="Profile picture"
                    >

                    <span>Change photo</span>
                </label>

                <input 
                    id="profile-image-input"
                    class="popup-file-input"
                    type="file" 
                    name="profile_image"
                    accept="image/png, image/jpeg, image/webp, image/avif"
                >

                <label>
                    Name
                    <input 
                        type="text" 
                        name="name" 
                        value="<?= htmlspecialchars($user->getName()) ?>"
                        required
                    >
                </label>

                <label>
                    Username
                    <input 
                        type="text" 
                        name="username" 
                        value="<?= htmlspecialchars($user->getUserName()) ?>"
                        required
                    >
                </label>

                <section class="popup-password">
                    <h2>Change Password</h2>

                    <label>
                        Current Password
                        <input 
                            type="password" 
                            name="current_password"
                            placeholder="Current password"
                        >
                    </label>

                    <label>
                        New Password
                        <input 
                            type="password" 
                            name="new_password"
                            placeholder="New password"
                        >
                    </label>

                    <label>
                        Confirm New Password
                        <input 
                            type="password" 
                            name="confirm_password"
                            placeholder="Confirm new password"
                        >
                    </label>
                </section>

                <div class="popup-actions">
                    <button 
                        type="button" 
                        class="btn small"
                        data-dialog-close
                    >
                        Cancel
                    </button>

                    <button type="submit" class="btn small light">
                        Save Changes
                    </button>
                </div>
            </form>
        </section>
    </dialog>
<?php }


function drawEditTrainerProfileDialog(Trainers $trainer): void { ?>
    <dialog id="edit-trainer-profile-dialog" class="popup-dialog">
        <section class="card popup-card">
            <button 
                type="button" 
                class="popup-close" 
                data-dialog-close
                aria-label="Close edit trainer profile dialog"
            >
                &times;
            </button>

            <header class="popup-header">
                <p class="profile-member-card-label">PowerPIT Trainer</p>
                <h1>Edit Trainer Profile</h1>
                <p>Update your public trainer information</p>
            </header>

            <form 
                class="popup-form" 
                action="../actions/action_edit_trainer_profile.php" 
                method="post"
            >
                <label>
                    Bio
                    <textarea 
                        name="bio"
                        placeholder="Write your trainer bio"
                    ><?= htmlspecialchars($trainer->getBio() ?? '') ?></textarea>
                </label>

                <label>
                    Specializations
                    <input 
                        type="text" 
                        name="specializations" 
                        value="<?= htmlspecialchars($trainer->getSpecializations() ?? '') ?>"
                        placeholder="Strength, HIIT, Pilates..."
                    >
                </label>

                <label>
                    Certifications
                    <input 
                        type="text" 
                        name="certifications" 
                        value="<?= htmlspecialchars($trainer->getCertifications() ?? '') ?>"
                        placeholder="Personal Trainer Level 3, CPR..."
                    >
                </label>

                <div class="popup-actions">
                    <button 
                        type="button" 
                        class="btn small"
                        data-dialog-close
                    >
                        Cancel
                    </button>

                    <button type="submit" class="btn small light">
                        Save Trainer Profile
                    </button>
                </div>
            </form>
        </section>
    </dialog>
<?php }


function drawProfile(PDO $db, Users $user): void {
    if ($user->getRole() === 'trainer') {
        $trainer = Trainers::getTrainerByUserId($db, $user->getUserId());

        if ($trainer !== null) {
            drawTrainerProfile($db, $user, $trainer, true);
            return;
        }
    }

    drawMemberProfile($db, $user);
}


function drawMemberProfile(PDO $db, Users $user): void {
    $nextClasses = $user->getWorkoutNextClasses($db);
    $classHistory = $user->getWorkoutClassHistory($db);
?>
    <main>
        <section class="flex-row light">
            <div class="flex-item">
                <div class="card card--dark">
                    <div class="profile-member-card-content">
                        <img 
                            class="profile-image-preview"
                            src="../assets/users/<?= htmlspecialchars($user->getProfileImage()) ?>" 
                            alt="Profile picture" 
                            width="200" 
                            height="100"
                        >

                        <div>
                            <p class="profile-member-card-label">PowerPIT Member</p>
                            <h1><?= htmlspecialchars($user->getName()) ?></h1>
                            <p class="profile-member-card-meta">
                                <?= htmlspecialchars(ucfirst($user->getRole())) ?> Account
                            </p>
                        </div>

                        <button 
                            type="button" 
                            class="btn small light profile-edit-btn"
                            data-dialog-target="edit-profile-dialog"
                        >
                            Edit Profile
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid">
            <article class="card">
                <h2 class="card-title center">Personal Information</h2>

                <dl>
                    <div class="card-dl-row">
                        <dt>Username</dt>
                        <dd><?= htmlspecialchars($user->getUserName()) ?></dd>
                    </div>

                    <div class="card-dl-row">
                        <dt>Email</dt>
                        <dd><?= htmlspecialchars($user->getEmail()) ?></dd>
                    </div>

                    <div class="card-dl-row">
                        <dt>Role</dt>
                        <dd><?= htmlspecialchars(ucfirst($user->getRole())) ?></dd>
                    </div>

                    <div class="card-dl-row">
                        <dt>Plan</dt>
                        <dd>Standard</dd>
                    </div>
                </dl>
            </article>

            <article class="card">
                <h2 class="card-title center">Next Classes</h2>

                <?php if (empty($nextClasses)) { ?>
                    <p>You do not have any booked classes yet.</p>
                <?php } else { ?>
                    <dl>
                        <?php foreach ($nextClasses as $workoutClass) { ?>
                            <div class="card-dl-row">
                                <dt><?= htmlspecialchars(getWorkoutClassDisplayName($db, $workoutClass)) ?></dt>
                                <dd>
                                    <button 
                                        type="button" 
                                        class="btn small light profile-edit-btn"
                                        data-dialog-target=""
                                    >
                                        Cancel
                                    </button>
                                    <?= htmlspecialchars(date('d M · H:i', strtotime($workoutClass->getClassDateTime()))) ?>
                                </dd>
                            </div>
                        <?php } ?>
                    </dl>
                <?php } ?>
            </article>

            <article class="card">
                <h2 class="card-title center">Classes History</h2>

                <?php if (empty($classHistory)) { ?>
                    <p>You have not attended any classes yet.</p>
                <?php } else { ?>
                    <dl>
                        <?php foreach ($classHistory as $workoutClass) {
                             $dialogId = 'review-dialog-' . $workoutClass->getId();
                             $classType = WorkoutClassType::getWorkoutClassType($db, $workoutClass->getClassTypeId());
                             drawReviewDialog($db, $classType, $workoutClass);
                             ?>
                            <div class="card-dl-row">
                                <dt><?= htmlspecialchars(getWorkoutClassDisplayName($db, $workoutClass)) ?></dt>
                            
                                <dd>
                                    <button 
                                        type="button" 
                                        class="btn small light profile-edit-btn"
                                        data-dialog-target="<?=htmlspecialchars($dialogId)?>"
                                    >
                                        Review
                                    </button>
                                    <?= htmlspecialchars(date('d M · H:i', strtotime($workoutClass->getClassDateTime()))) ?>
                                </dd>
                            </div>
                        <?php } ?>
                    </dl>
                <?php } ?>
            </article>
        </section>

        <?php drawEditProfileDialog($user);?>
    </main>
<?php }


function drawTrainerProfile(PDO $db, Users $user, Trainers $trainer, bool $canEdit): void {
    $assignedClasses = $trainer->getAssignedClasses($db);
    $avgRatings = $trainer->getAverageRatings($db);
    $avgRating = $avgRatings['AverageRating'];
    $ratingCount = $avgRatings['TotalReviews'];
    $reviews = $trainer->getReviews($db);
?>
    <main>
        <section class="flex-row light">
            <div class="flex-item">
                <div class="card card--dark">
                    <div class="profile-member-card-content">
                        <img 
                            class="profile-image-preview"
                            src="../assets/users/<?= htmlspecialchars($user->getProfileImage()) ?>" 
                            alt="Trainer profile picture" 
                            width="200" 
                            height="100"
                        >

                        <div>
                            <p class="profile-member-card-label">PowerPIT Trainer</p>
                            <h1><?= htmlspecialchars($user->getName()) ?></h1>
                            <p class="profile-member-card-meta">
                                <?php if ($canEdit) { ?>
                                    @<?= htmlspecialchars($user->getUserName()) ?> · <?= htmlspecialchars($user->getEmail()) ?>
                                <?php } else { ?>
                                    @<?= htmlspecialchars($user->getUserName()) ?>
                                <?php } ?>
                            </p>
                        </div>

                        <?php if ($canEdit) { ?>
                            <button 
                                type="button" 
                                class="btn small light profile-edit-btn"
                                data-dialog-target="edit-profile-dialog"
                            >
                                Edit Profile
                            </button>
                        <?php } ?>
                    </div>

                    <div class="trainer_profile_extra">
                        <p>
                            <?= htmlspecialchars($trainer->getBio() ?? 'No trainer bio added yet.') ?>
                        </p>

                        <div class="trainer_review">
                            <strong><?= htmlspecialchars((string)$avgRating) ?> / 5</strong>
                            <span><?php if (is_null($ratingCount)) { ?>
                                    There are no reviews yet.
                                <?php } else if ($ratingCount == 1) { ?>
                                    <?= htmlspecialchars('Based on a member review.') ?>
                                <?php } else { ?>
                                    <?= htmlspecialchars('Based on ' . $ratingCount . ' member reviews.') ?>
                                <?php } ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid">
            <?php drawTrainerPublicCard($trainer, $canEdit); ?>
            <?php drawTrainerScheduleCard($db, $assignedClasses); ?>
        </section>

        <section class="grid">
            <?php drawTrainerRosterCard($db, $assignedClasses); ?>
        </section>

        <section class="grid">
            <?php drawTrainerReviews($db, $reviews, $canEdit); ?>
        </section>

        <?php if ($canEdit) { ?>
            
            <?php drawEditProfileDialog($user); ?>
            <?php drawEditTrainerProfileDialog($trainer); ?>
        <?php } ?>
    </main>
<?php }


function drawTrainerPublicCard(Trainers $trainer, bool $canEdit): void {
    $specializations = splitTrainerText($trainer->getSpecializations());
?>
    <article class="card">
        <h2 class="card-title center">Trainer Information</h2>

        <h3>Specializations</h3>

        <?php if (empty($specializations)) { ?>
            <p>No specializations added yet.</p>
        <?php } else { ?>
            <ul class="trainer_tags">
                <?php foreach ($specializations as $specialization) { ?>
                    <li><?= htmlspecialchars($specialization) ?></li>
                <?php } ?>
            </ul>
        <?php } ?>

        <p class="trainer_certifications">
            <strong>Certifications:</strong>
            <?= htmlspecialchars($trainer->getCertifications() ?? 'No certifications added yet.') ?>
        </p>

        <?php if ($canEdit) { ?>
            <button 
                type="button" 
                class="btn small light trainer-public-edit-btn"
                data-dialog-target="edit-trainer-profile-dialog"
            >
                Edit Trainer Information
            </button>
        <?php } ?>
    </article>
<?php }


function drawTrainerScheduleCard(PDO $db, array $assignedClasses): void { ?>
    <article class="card">
        <h2 class="card-title center">Assigned Schedule</h2>

        <?php if (empty($assignedClasses)) { ?>
            <p>You do not have any assigned classes yet.</p>
        <?php } else { ?>
            <dl>
                <?php foreach ($assignedClasses as $workoutClass) { 
                    $timestamp = strtotime($workoutClass->getClassDateTime());
                    $members = Enrollments::getMembersByClassId($db, $workoutClass->getId());
                ?>
                    <div class="card-dl-row">
                        <dt><?= htmlspecialchars(getWorkoutClassDisplayName($db, $workoutClass)) ?></dt>
                        <dd>
                            <?= htmlspecialchars(date('d M · H:i', $timestamp)) ?>
                            · <?= htmlspecialchars((string)count($members)) ?>/<?= htmlspecialchars((string)$workoutClass->getCapacity()) ?> members
                        </dd>
                    </div>
                <?php } ?>
            </dl>
        <?php } ?>
    </article>
<?php }


function drawTrainerRosterCard(PDO $db, array $assignedClasses): void { ?>
    <article class="card trainer_roster_card">
        <h2 class="card-title center">Class Rosters</h2>

        <?php if (empty($assignedClasses)) { ?>
            <p>No rosters available yet.</p>
        <?php } else { ?>
            <?php foreach ($assignedClasses as $workoutClass) {
                $members = Enrollments::getMembersByClassId($db, $workoutClass->getId());
                $timestamp = strtotime($workoutClass->getClassDateTime());
            ?>
                <section class="trainer_roster_group">
                    <header class="trainer_roster_header">
                        <h3><?= htmlspecialchars(getWorkoutClassDisplayName($db, $workoutClass)) ?></h3>
                        <p>
                            <?= htmlspecialchars(date('d M · H:i', $timestamp)) ?>
                            · <?= htmlspecialchars((string)count($members)) ?>/<?= htmlspecialchars((string)$workoutClass->getCapacity()) ?> members
                        </p>
                    </header>

                    <?php if (empty($members)) { ?>
                        <p>No members enrolled in this class yet.</p>
                    <?php } else { ?>
                        <ul class="trainer_roster_members">
                            <?php foreach ($members as $member) { ?>
                                <li>
                                    <img 
                                        src="../assets/users/<?= htmlspecialchars($member->getProfileImage()) ?>" 
                                        alt="Member profile picture"
                                    >

                                    <div>
                                        <strong><?= htmlspecialchars($member->getName()) ?></strong>
                                        <span>@<?= htmlspecialchars($member->getUserName()) ?></span>
                                    </div>

                                    <span><?= htmlspecialchars($member->getEmail()) ?></span>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </section>
            <?php } ?>
        <?php } ?>
    </article>
<?php }

function drawReviewDialog(PDO $db,WorkoutClassType $workoutClassType, WorkoutClass $workoutClass): void {
    $timestamp = strtotime($workoutClass->getClassDateTime());

    $day = date('l', $timestamp);
    $date = date('d M Y', $timestamp);
    $time = date('H:i', $timestamp);

    $dialogId = 'review-dialog-' . $workoutClass->getId();
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
                <p class="profile-member-card-label"><?= htmlspecialchars($workoutClassType->getName())?> Class</p>
                <h1>Review Class</h1>
                <p>Rate this class and give us your feedback.</p>
            </header>

            <dl>
                <h2>Info</h2>
                <div class="card-dl-row">
                    <dt>Class</dt>
                    <dd><?= htmlspecialchars($workoutClassType->getName()) ?></dd>
                </div>

                <div class="card-dl-row">
                    <dt>Date</dt>
                    <dd><?= htmlspecialchars($date)?> · <?=htmlspecialchars($time) ?></dd>
                </div>

                <div class="card-dl-row">
                    <dt>Trainer</dt>
                    <dd><?= htmlspecialchars((string)$workoutClass->getTrainerName($db)) ?></dd>
                </div>
            </dl>

            <form 
                class="popup-form review-form"
                action="../actions/action_review.php" 
                method="post"
            >
                <input 
                    type="hidden" 
                    name="class_id" 
                    value="<?= htmlspecialchars((string)$workoutClass->getId()) ?>"
                >

                <h2>Review</h2>

                <label>
                    Rating:
                    <div class="rating-input">

                        <input type="radio" name="rating" id="star5-<?= htmlspecialchars((string)$workoutClass->getId()) ?>" value="5">
                        <label for="star5-<?= htmlspecialchars((string)$workoutClass->getId()) ?>">★</label>

                        <input type="radio" name="rating" id="star4-<?= htmlspecialchars((string)$workoutClass->getId()) ?>" value="4">
                        <label for="star4-<?= htmlspecialchars((string)$workoutClass->getId()) ?>">★</label>

                        <input type="radio" name="rating" id="star3-<?= htmlspecialchars((string)$workoutClass->getId()) ?>" value="3">
                        <label for="star3-<?= htmlspecialchars((string)$workoutClass->getId()) ?>">★</label>

                        <input type="radio" name="rating" id="star2-<?= htmlspecialchars((string)$workoutClass->getId()) ?>" value="2">
                        <label for="star2-<?= htmlspecialchars((string)$workoutClass->getId()) ?>">★</label>

                        <input type="radio" name="rating" id="star1-<?= htmlspecialchars((string)$workoutClass->getId()) ?>" value="1">
                        <label for="star1-<?= htmlspecialchars((string)$workoutClass->getId()) ?>">★</label>

                    </div>
                </label>

                <label>
                    Review:
                    <textarea 
                        name="review"
                        placeholder="Tell us your opinion!"
                    ></textarea>
                </label>

                

                <div class="popup-actions">
                    <button 
                        type="button" 
                        class="btn small"
                        data-dialog-close
                    >
                        Cancel
                    </button>

                    <button type="submit" class="btn small light">
                        Confirm Review
                    </button>
                </div>
            </form>
        </section>
    </dialog>
<?php } 
function drawTrainerReviews($db, $reviews, $canEdit): void { ?>
    <article class="card">
        <h2 class="card-title center">Class Reviews</h2>
        <?php if (empty($reviews)) { 
            if ($canEdit) { ?>
            <p>You do not have any reviews yet.</p>
            <?php } else { ?>
            <p>This trainer does not have any reviews yet.</p>
        <?php }
        } else { ?>

            <ul class="trainer_roster_members">
                <?php foreach ($reviews as $review) { ?>
                    
                        <li>
                            <img 
                                src="../assets/users/<?= htmlspecialchars($review['ProfileImage'] ?? 'default.png') ?>" 
                                alt="Member profile picture"
                            >

                            <div>
                                <strong>
                                    <?= htmlspecialchars($review['Name']) ?>
                                    <?= renderStars($review['Rating']) ?>
                                </strong>
                                <span>@<?= htmlspecialchars($review['Username']) ?></span>
                            </div>

                            <span><?= htmlspecialchars($review['ClassType'] . ' Class of ' . date('d M · H:i', strtotime($review['ClassDateTime']))) ?></span>

                            <p><?= htmlspecialchars($review['Review']) ?></p>
                        </li>
                    
                <?php } ?>
            </ul>
        <?php } ?>
    </article>
<?php } ?>