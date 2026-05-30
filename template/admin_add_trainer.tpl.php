<?php
declare(strict_types = 1);

function drawAdminAddTrainerPage(): void { ?>
    <main class="page-shell admin-users-page">

        <section class="hero-panel admin-users-hero">
            <p class="admin-label">PowerPIT Admin</p>

            <h1>Add Trainer</h1>

            <p>
                Search existing members and promote one of them to trainer.
            </p>
        </section>

        <section class="toolbar admin-users-controls admin-users-controls-row">
            <input
                type="text"
                id="add-trainer-search-input"
                placeholder="Search members..."
                autocomplete="off"
            >

            <a href="admin_trainers.php" class="btn light btn-compact btn-icon">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                Back to Trainers
            </a>
        </section>

        <section class="data-list admin-users-results">
            <div class="data-header admin-users-header">
                <span>User</span>
                <span>Email</span>
                <span>Current Role</span>
                <span>Actions</span>
            </div>

            <div id="add-trainer-search-results" class="data-list-body user-search-results">
                <p class="empty-search-message">
                    Loading available members...
                </p>
            </div>
        </section>

    </main>

    <script src="../js/admin_confirm.js" defer></script>
    <script src="../js/search_add_trainer.js" defer></script>
<?php } ?>