<?php
declare(strict_types = 1);

function drawAdminSearchUserPage(): void { ?>
    <main class="admin-users-page">
        <?php drawAdminSearchUserHero(); ?>
        <?php drawAdminSearchUserControls(); ?>
        <?php drawAdminSearchUserResults(); ?>
    </main>

    <script src="../js/admin_search_user.js" defer></script>
<?php } ?>


<?php
function drawAdminSearchUserHero(): void { ?>
    <section class="admin-users-hero">
        <p class="admin-label">PowerPit Admin</p>
        <h1>Search Users</h1>
        <p>Search platform users by name, username or email.</p>
    </section>
<?php } ?>


<?php
function drawAdminSearchUserControls(): void { ?>
    <section class="admin-users-controls">
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
    <section class="admin-users-results">
        <div class="admin-users-header">
            <span>User</span>
            <span>Email</span>
            <span>Role</span>
            <span>Actions</span>
        </div>

        <div id="user-search-results" class="user-search-results">
            <p class="empty-search-message">Loading users...</p>
        </div>
    </section>
<?php } ?>