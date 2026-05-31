<?php
declare(strict_types = 1);

function drawAdminSearchUserPage(): void { ?>
    <main class="page-shell admin-page">
        <?php drawAdminSearchUserHero(); ?>
        <?php drawAdminSearchUserControls(); ?>
        <?php drawAdminSearchUserResults(); ?>
    </main>

<?php } ?>


<?php
function drawAdminSearchUserHero(): void { ?>
    <section class="hero-panel admin-manage-hero">
        <p class="admin-label">PowerPit Admin</p>
        <h1>Search Users</h1>
        <p>Search platform users by name, username or email.</p>
    </section>
<?php } ?>


<?php
function drawAdminSearchUserControls(): void { ?>
    <section class="toolbar admin-users-controls">
        <input
            type="text"
            id="user-search-input"
            name="q"
            placeholder="Search users..."
            autocomplete="off"
        >
    </section>
<?php } ?>


<?php
function drawAdminSearchUserResults(): void { ?>
    <section class="data-list admin-users-results">
        <div class="data-header admin-header">
            <span>User</span>
            <span>Email</span>
            <span>Role</span>
            <span>Actions</span>
        </div>

        <div id="user-search-results" class="data-list-body user-search-results">
            <p class="empty-search-message">Loading users...</p>
        </div>
    </section>
<?php } ?>