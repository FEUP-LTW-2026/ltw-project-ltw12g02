<?php
declare(strict_types = 1);

function drawAdminAddTrainerPage(): void { ?>
    <main class="admin-users-page">

        <section class="admin-users-hero">
            <p class="admin-label">PowerPIT Admin</p>

            <h1>Add Trainer</h1>

            <p>
                Search existing members and promote one of them to trainer.
            </p>
        </section>

        <section class="admin-users-controls admin-users-controls-row">
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

        <section class="admin-users-results">
            <div class="admin-users-header">
                <span>User</span>
                <span>Email</span>
                <span>Current Role</span>
                <span>Actions</span>
            </div>

            <div id="add-trainer-search-results" class="user-search-results">
                <p class="empty-search-message">
                    Loading available members...
                </p>
            </div>
        </section>

    </main>

    <script src="../js/admin_confirm.js" defer></script>
    <script src="../js/search_add_trainer.js" defer></script>
<?php } ?>