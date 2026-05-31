<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/csrf.php');

function drawAdminClassesPage(
    array $classes,
    array $classTypes,
    array $trainers,
    string $filter,
    PDO $db
): void { ?>
    <main class="page-shell admin-page admin-classes-page">
        <?php drawAdminClassesHero(); ?>
        <?php drawAdminClassesControls($trainers, $filter, $db); ?>
        <?php drawAdminClassFilters($filter); ?>
        <?php drawAdminClassesResults($classes, $db); ?>

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

    <script src="../js/filter_classes.js" defer></script>
<?php } ?>


<?php
function drawAdminClassesHero(): void { ?>
    <section class="hero-panel admin-manage-hero">
        <p class="admin-label">PowerPit Admin</p>
        <h1>Manage Classes</h1>
        <p>Create classes, assign trainers, edit schedules and manage capacity.</p>
    </section>
<?php } ?>


<?php
function drawAdminClassesControls(array $trainers, string $filter, PDO $db): void {
    $dateMin = $filter === 'upcoming' ? date('Y-m-d') : '';
    $dateMax = $filter === 'past' ? date('Y-m-d') : '';
?>
    <section class="toolbar admin-users-controls admin-users-controls-row">
        <form class="toolbar-form filter-form admin-class-filter-form">
            <input type="hidden" name="admin" value="1">
            <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">

            <select name="trainer">
                <option value="">All trainers</option>

                <?php foreach ($trainers as $trainer) { ?>
                    <option value="<?= htmlspecialchars((string) $trainer->getTrainerId()) ?>">
                        <?= htmlspecialchars($trainer->getName($db)) ?>
                    </option>
                <?php } ?>
            </select>

            <input
                type="date"
                name="date"
                <?= $dateMin !== '' ? 'min="' . htmlspecialchars($dateMin) . '"' : '' ?>
                <?= $dateMax !== '' ? 'max="' . htmlspecialchars($dateMax) . '"' : '' ?>
            >

            <input
                type="time"
                name="time"
            >
        </form>

        <button
            type="button"
            class="btn light btn-compact btn-icon"
            data-dialog-target="admin-class-create-dialog"
        >
            <i class="fa fa-plus" aria-hidden="true"></i>
            Add class
        </button>
    </section>
<?php } ?>


<?php
function drawAdminClassFilters(string $filter): void { ?>
    <nav class="admin-class-filters" aria-label="Class filters">
        <a
            href="admin_classes.php?filter=all"
            class="pill admin-role <?= $filter === 'all' ? 'active' : '' ?>"
        >
            All classes
        </a>

        <a
            href="admin_classes.php?filter=upcoming"
            class="pill admin-role <?= $filter === 'upcoming' ? 'active' : '' ?>"
        >
            Upcoming
        </a>

        <a
            href="admin_classes.php?filter=past"
            class="pill admin-role <?= $filter === 'past' ? 'active' : '' ?>"
        >
            Past
        </a>
    </nav>
<?php } ?>


<?php
function drawAdminClassesResults(array $classes, PDO $db): void { ?>
    <section class="data-list admin-users-results">
        <div class="data-header admin-header admin-class-row-layout">
            <span>Class</span>
            <span>Schedule</span>
            <span>Trainer</span>
            <span>Bookings</span>
            <span>Actions</span>
        </div>

        <div id="available-classes" class="data-list-body user-search-results">
            <?php if (empty($classes)) { ?>
                <p class="empty-search-message">No classes found.</p>
            <?php } ?>

            <?php foreach ($classes as $class) { ?>
                <?php drawAdminClassRow($class, $db); ?>
            <?php } ?>
        </div>
    </section>
<?php } ?>


<?php
function drawAdminClassRow(WorkoutClass $class, PDO $db): void {
    $classType = WorkoutClassType::getWorkoutClassType($db, $class->getClassTypeId());

    $classTypeName = $classType !== null ? $classType->getName() : 'Unknown class';
    $duration = $classType !== null ? $classType->getDuration() : 0;

    $enrollments = WorkoutClass::getEnrollmentCount($db, $class->getId());
    $capacity = $class->getCapacity();

    $date = formatAdminDate($class->getClassDateTime());
    $time = formatAdminTime($class->getClassDateTime());
    $trainerName = $class->getTrainerName($db);
?>
    <article class="data-row admin-user-row admin-class-row-layout">
        <div class="avatar-title admin-user-main">
            <div class="icon-box admin-icon-box">
                <i class="fa fa-calendar" aria-hidden="true"></i>
            </div>

            <div>
                <strong><?= htmlspecialchars($classTypeName) ?></strong>
                <p><?= htmlspecialchars((string) $duration) ?> min</p>
            </div>
        </div>

        <div class="admin-user-email">
            <strong><?= htmlspecialchars($date) ?></strong>
            <p><?= htmlspecialchars($time) ?></p>
        </div>

        <p class="admin-user-email">
            <?= htmlspecialchars($trainerName) ?>
        </p>

        <span class="pill admin-role">
            <?= htmlspecialchars((string) $enrollments) ?>/<?= htmlspecialchars((string) $capacity) ?>
            booked
        </span>

        <div class="row-actions admin-user-actions">
            <button
                type="button"
                data-dialog-target="admin-class-edit-dialog-<?= htmlspecialchars((string) $class->getId()) ?>"
            >
                Edit
            </button>

            <button
                type="button"
                class="admin-action-secondary"
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
    $minimumDateTime = date('Y-m-d\TH:i');
?>
    <dialog id="<?= htmlspecialchars($dialogId) ?>" class="modal popup-dialog">
        <section class="modal-card card popup-card" id="admin-class-dialog">
            <button
                type="button"
                class="popup-close"
                data-dialog-close
                aria-label="Close dialog"
            >
                &times;
            </button>

            <header class="popup-header">
                <p class="title-label">PowerPIT Admin</p>
                <h1><?= htmlspecialchars($title) ?></h1>
                <p>Choose the class type, trainer, schedule and capacity.</p>
            </header>

            <form
                class="form-stack popup-form"
                
                action="../actions/action_admin_class.php"
                method="post"
            >
                <?php sendCSRF(); ?>
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
                    min="<?= htmlspecialchars($minimumDateTime) ?>"
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

                <div class="actions-row popup-actions">
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

    $enrollments = WorkoutClass::getEnrollmentCount($db, $class->getId());
?>
    <dialog
        id="admin-class-delete-dialog-<?= htmlspecialchars((string) $class->getId()) ?>"
        class="modal popup-dialog"
    >
        <section class="modal-card card popup-card">
            <button
                type="button"
                class="popup-close"
                data-dialog-close
                aria-label="Close dialog"
            >
                &times;
            </button>

            <header class="popup-header">
                <p class="title-label">PowerPIT Admin</p>
                <h1>Delete Class</h1>
                <p>This action will remove this class from the schedule.</p>
            </header>

            <form
                class="form-stack popup-form"
                action="../actions/action_admin_class.php"
                method="post"
            >
                <?php sendCSRF(); ?>
                <input type="hidden" name="action" value="delete">

                <input
                    type="hidden"
                    name="class_id"
                    value="<?= htmlspecialchars((string) $class->getId()) ?>"
                >

                <article class="card">
                    <p class="title-label">Class selected</p>
                    <h1><?= htmlspecialchars($classTypeName) ?></h1>

                    <div class="card-wrap">
                        <p><?= htmlspecialchars($class->getTrainerName($db)) ?></p>

                        <p>
                            <?= htmlspecialchars(formatAdminDate($class->getClassDateTime())) ?>
                            at
                            <?= htmlspecialchars(formatAdminTime($class->getClassDateTime())) ?>
                        </p>

                        <p>
                            <?= htmlspecialchars((string) $enrollments) ?>/<?= htmlspecialchars((string) $class->getCapacity()) ?>
                            booked
                        </p>
                    </div>
                </article>

                <div class="actions-row popup-actions">
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