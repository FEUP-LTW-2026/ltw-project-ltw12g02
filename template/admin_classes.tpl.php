<?php
declare(strict_types = 1);

function drawAdminClassesPage(
    array $classes,
    array $classTypes,
    array $trainers,
    string $filter,
    PDO $db
): void { ?>
    <main class="admin-users-page admin-classes-page">

        <section class="admin-users-hero">
            <p class="admin-label">Schedule Center</p>

            <h1>Manage Classes</h1>

            <p>
                Create classes, assign trainers, edit schedules and manage capacity.
            </p>

            <div class="admin-class-toolbar">
                <button
                    type="button"
                    class="btn small light"
                    data-dialog-target="admin-class-create-dialog"
                >
                    Add class
                </button>

                <a href="admin.php" class="btn small">
                    Back to dashboard
                </a>
            </div>
        </section>

        <section class="admin-class-filters">
            <a
                href="admin_classes.php?filter=all"
                class="<?= $filter === 'all' ? 'active' : '' ?>"
            >
                All classes
            </a>

            <a
                href="admin_classes.php?filter=upcoming"
                class="<?= $filter === 'upcoming' ? 'active' : '' ?>"
            >
                Upcoming
            </a>

            <a
                href="admin_classes.php?filter=past"
                class="<?= $filter === 'past' ? 'active' : '' ?>"
            >
                Past
            </a>
        </section>

        <?php if (empty($classTypes) || empty($trainers)) { ?>
            <article class="empty-search-message">
                You need at least one class type and one trainer before creating classes.
            </article>
        <?php } ?>

        <section class="admin-users-results">
            <div class="admin-users-header admin-classes-header">
                <span>Class</span>
                <span>Schedule</span>
                <span>Trainer</span>
                <span>Capacity</span>
                <span></span>
            </div>

            <div class="user-search-results">
                <?php if (empty($classes)) { ?>
                    <article class="empty-search-message">
                        No classes found.
                    </article>
                <?php } else { ?>
                    <?php foreach ($classes as $class) { ?>
                        <?php drawAdminClassRow($class, $db); ?>
                    <?php } ?>
                <?php } ?>
            </div>
        </section>

        <?php drawAdminClassFormDialog(
            'admin-class-create-dialog',
            'Add Class',
            'create',
            $classTypes,
            $trainers,
            $db
        ); ?>

        <?php foreach ($classes as $class) { ?>
            <?php drawAdminClassFormDialog(
                'admin-class-edit-dialog-' . $class->getId(),
                'Edit Class',
                'update',
                $classTypes,
                $trainers,
                $db,
                $class
            ); ?>

            <?php drawAdminClassDeleteDialog($class, $db); ?>
        <?php } ?>

    </main>
<?php } ?>


<?php
function drawAdminClassRow(WorkoutClass $class, PDO $db): void {
    $classType = WorkoutClassType::getWorkoutClassType($db, $class->getClassTypeId());

    $classTypeName = $classType !== null ? $classType->getName() : 'Unknown class';
    $duration = $classType !== null ? $classType->getDuration() : 0;

    $capacity = $class->getCapacity();
    $isFull = $class->isFull($db);
?>
    <article class="admin-user-row admin-class-row">
        <div class="admin-class-main">
            <div class="admin-icon-box">
                <i class="fa fa-calendar" aria-hidden="true"></i>
            </div>

            <div>
                <strong><?= htmlspecialchars($classTypeName) ?></strong>
                <p><?= htmlspecialchars((string) $duration) ?> min</p>
            </div>
        </div>

        <div class="admin-class-date">
            <strong><?= htmlspecialchars(formatAdminDate($class->getClassDateTime())) ?></strong>
            <p><?= htmlspecialchars(formatAdminTime($class->getClassDateTime())) ?></p>
        </div>

        <p class="admin-user-email">
            <?= htmlspecialchars($class->getTrainerName($db)) ?>
        </p>

        <span class="admin-user-role admin-class-capacity">
            <?= $isFull ? 'Full' : 'Available' ?>
            /
            <?= htmlspecialchars((string) $capacity) ?>
        </span>

        <div class="admin-user-actions admin-class-actions">
            <button
                type="button"
                data-dialog-target="admin-class-edit-dialog-<?= htmlspecialchars((string) $class->getId()) ?>"
            >
                Edit
            </button>

            <button
                type="button"
                class="admin-class-delete-btn"
                data-dialog-target="admin-class-delete-dialog-<?= htmlspecialchars((string) $class->getId()) ?>"
            >
                Delete
            </button>
        </div>
    </article>
<?php } ?>


<?php
function drawAdminClassFormDialog(
    string $dialogId,
    string $title,
    string $action,
    array $classTypes,
    array $trainers,
    PDO $db,
    ?WorkoutClass $class = null
): void {
    $selectedClassTypeId = $class !== null ? $class->getClassTypeId() : 0;
    $selectedTrainerId = $class !== null ? $class->getTrainerId() : 0;
    $capacity = $class !== null ? $class->getCapacity() : 1;
    $classDateTime = $class !== null ? formatAdminDateTimeInput($class->getClassDateTime()) : '';
?>
    <dialog id="<?= htmlspecialchars($dialogId) ?>" class="popup-dialog">
        <section class="card popup-card">
            <button
                type="button"
                class="popup-close"
                data-dialog-close
                aria-label="Close dialog"
            >
                &times;
            </button>

            <header class="popup-header">
                <p class="profile-member-card-label">PowerPIT Admin</p>
                <h1><?= htmlspecialchars($title) ?></h1>
                <p>Choose the class type, trainer, schedule and capacity.</p>
            </header>

            <form
                class="popup-form edit-profile-form"
                action="../actions/action_admin_class.php"
                method="post"
            >
                <input type="hidden" name="action" value="<?= htmlspecialchars($action) ?>">

                <?php if ($class !== null) { ?>
                    <input
                        type="hidden"
                        name="class_id"
                        value="<?= htmlspecialchars((string) $class->getId()) ?>"
                    >
                <?php } ?>

                <label for="<?= htmlspecialchars($dialogId) ?>-class-type">Class type</label>
                <select
                    id="<?= htmlspecialchars($dialogId) ?>-class-type"
                    name="class_type_id"
                    required
                >
                    <option value="">Choose a class type</option>

                    <?php foreach ($classTypes as $classType) { ?>
                        <option
                            value="<?= htmlspecialchars((string) $classType->getId()) ?>"
                            <?= $classType->getId() === $selectedClassTypeId ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($classType->getName()) ?>
                            -
                            <?= htmlspecialchars((string) $classType->getDuration()) ?> min
                        </option>
                    <?php } ?>
                </select>

                <label for="<?= htmlspecialchars($dialogId) ?>-trainer">Trainer</label>
                <select
                    id="<?= htmlspecialchars($dialogId) ?>-trainer"
                    name="trainer_id"
                    required
                >
                    <option value="">Choose a trainer</option>

                    <?php foreach ($trainers as $trainer) { ?>
                        <option
                            value="<?= htmlspecialchars((string) $trainer->getTrainerId()) ?>"
                            <?= $trainer->getTrainerId() === $selectedTrainerId ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($trainer->getName($db)) ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="<?= htmlspecialchars($dialogId) ?>-datetime">Date and time</label>
                <input
                    id="<?= htmlspecialchars($dialogId) ?>-datetime"
                    type="datetime-local"
                    name="class_datetime"
                    value="<?= htmlspecialchars($classDateTime) ?>"
                    required
                >

                <label for="<?= htmlspecialchars($dialogId) ?>-capacity">Capacity</label>
                <input
                    id="<?= htmlspecialchars($dialogId) ?>-capacity"
                    type="number"
                    name="capacity"
                    min="1"
                    value="<?= htmlspecialchars((string) $capacity) ?>"
                    required
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
                        Save class
                    </button>
                </div>
            </form>
        </section>
    </dialog>
<?php } ?>


<?php
function drawAdminClassDeleteDialog(WorkoutClass $class, PDO $db): void {
    $classType = WorkoutClassType::getWorkoutClassType($db, $class->getClassTypeId());
    $classTypeName = $classType !== null ? $classType->getName() : 'Unknown class';
?>
    <dialog
        id="admin-class-delete-dialog-<?= htmlspecialchars((string) $class->getId()) ?>"
        class="popup-dialog"
    >
        <section class="card popup-card">
            <button
                type="button"
                class="popup-close"
                data-dialog-close
                aria-label="Close dialog"
            >
                &times;
            </button>

            <header class="popup-header">
                <p class="profile-member-card-label">PowerPIT Admin</p>
                <h1>Delete Class</h1>
                <p>This action will remove this class from the schedule.</p>
            </header>

            <form
                class="popup-form edit-profile-form"
                action="../actions/action_admin_class.php"
                method="post"
            >
                <input type="hidden" name="action" value="delete">

                <input
                    type="hidden"
                    name="class_id"
                    value="<?= htmlspecialchars((string) $class->getId()) ?>"
                >

                <div class="admin-class-delete-summary">
                    <strong><?= htmlspecialchars($classTypeName) ?></strong>
                    <p><?= htmlspecialchars($class->getTrainerName($db)) ?></p>
                    <p>
                        <?= htmlspecialchars(formatAdminDate($class->getClassDateTime())) ?>
                        at
                        <?= htmlspecialchars(formatAdminTime($class->getClassDateTime())) ?>
                    </p>
                </div>

                <div class="popup-actions">
                    <button
                        type="button"
                        class="btn small"
                        data-dialog-close
                    >
                        Cancel
                    </button>

                    <button type="submit" class="btn small light">
                        Delete class
                    </button>
                </div>
            </form>
        </section>
    </dialog>
<?php } ?>


<?php
function formatAdminDateTimeInput(string $dateTime): string {
    $timestamp = strtotime($dateTime);

    if ($timestamp === false) {
        return '';
    }

    return date('Y-m-d\TH:i', $timestamp);
}

function formatAdminDate(string $dateTime): string {
    $timestamp = strtotime($dateTime);

    if ($timestamp === false) {
        return 'Invalid date';
    }

    return date('d M Y', $timestamp);
}

function formatAdminTime(string $dateTime): string {
    $timestamp = strtotime($dateTime);

    if ($timestamp === false) {
        return 'Invalid time';
    }

    return date('H:i', $timestamp);
}
?>