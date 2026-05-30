<?php
declare(strict_types = 1);

function drawAdminTrainersPage(array $trainers): void { ?>
    <main class="page-shell admin-users-page">
        <?php drawAdminTrainersHero(); ?>
        <?php drawAdminTrainersControls(); ?>
        <?php drawAdminTrainersResults($trainers); ?>
    </main>
<?php } ?>


<?php
function drawAdminTrainersHero(): void { ?>
    <section class="hero-panel admin-users-hero">
        <p class="admin-label">PowerPit Admin</p>
        <h1>Manage Trainers</h1>
        <p>Search, review and manage trainer profiles.</p>
    </section>
<?php } ?>


<?php
function drawAdminTrainersControls(): void { ?>
    <section class="toolbar admin-users-controls admin-users-controls-row">
        <input
            type="text"
            id="trainer-search-input"
            placeholder="Search trainers..."
            autocomplete="off"
        >

        <a href="admin_add_trainer.php" class="btn light btn-compact btn-icon">
            <i class="fa fa-plus" aria-hidden="true"></i>
            Add Trainer
        </a>
    </section>
<?php } ?>


<?php
function drawAdminTrainersResults(array $trainers): void { ?>
    <section class="data-list admin-users-results">
        <div class="data-header admin-users-header admin-trainers-header">
            <span>Trainer</span>
            <span>Email</span>
            <span>Specialization</span>
            <span>Actions</span>
        </div>

        <div id="trainer-search-results" class="data-list-body user-search-results">
            <?php if (empty($trainers)) { ?>
                <p class="empty-search-message">No trainers found.</p>
            <?php } ?>

            <?php foreach ($trainers as $trainer) { ?>
                <?php drawAdminTrainerRow($trainer); ?>
            <?php } ?>
        </div>
    </section>
<?php } ?>


<?php
function drawAdminTrainerRow(array $trainer): void {
    $profileImage = $trainer['ProfileImage'] ?: 'default.png';

    $searchText = strtolower(
        $trainer['Name'] . ' ' .
        $trainer['Username'] . ' ' .
        $trainer['Email'] . ' ' .
        ($trainer['Specializations'] ?? '') . ' ' .
        ($trainer['Certifications'] ?? '')
    );
?>
    <article 
        class="data-row admin-user-row admin-trainer-row"
        data-search="<?= htmlspecialchars($searchText) ?>"
    >
        <div class="avatar-title admin-user-main">
            <img 
                src="../assets/users/<?= htmlspecialchars($profileImage) ?>" 
                alt="Trainer picture"
            >

            <div>
                <strong><?= htmlspecialchars($trainer['Name']) ?></strong>
                <p>@<?= htmlspecialchars($trainer['Username']) ?></p>
            </div>
        </div>

        <p class="admin-user-email">
            <?= htmlspecialchars($trainer['Email']) ?>
        </p>

        <span class="pill admin-user-role">
            <?= htmlspecialchars($trainer['Specializations'] ?: 'Trainer') ?>
        </span>

        <div class="row-actions admin-user-actions">
            <a href="admin_edit_trainer.php?id=<?= htmlspecialchars((string) $trainer['TrainerId']) ?>">
                Edit
            </a>
        </div>
    </article>
<?php } ?>