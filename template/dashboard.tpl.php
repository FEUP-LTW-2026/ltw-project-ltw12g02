<?php
declare(strict_types = 1);

function drawAdminDashboard(string $adminName, array $stats): void { ?>
    <main class="admin-dashboard">
        <?php drawAdminHero($adminName); ?>
        <?php drawAdminStats($stats); ?>
        <?php drawAdminManagementCards(); ?>
        <?php drawAdminQuickActions(); ?>
    </main>
<?php } ?>

<?php
function drawAdminHero(string $adminName): void { ?>
    <section class="admin-hero">
        <div>
            <p class="admin-label">PowerPit Admin</p>
            <h1>Welcome, <?= htmlspecialchars($adminName) ?></h1>
            <p>Manage users, trainers, classes and gym equipment from one place.</p>
        </div>
    </section>
<?php } ?>

<?php
function drawAdminStats(array $stats): void { ?>
    <section class="admin-stats">
        <article class="admin-stat-card">
            <span><?= htmlspecialchars((string) $stats['users']) ?></span>
            <p>Users</p>
        </article>

        <article class="admin-stat-card">
            <span><?= htmlspecialchars((string) $stats['trainers']) ?></span>
            <p>Trainers</p>
        </article>

        <article class="admin-stat-card">
            <span><?= htmlspecialchars((string) $stats['classes']) ?></span>
            <p>Classes</p>
        </article>

        <article class="admin-stat-card">
            <span><?= htmlspecialchars((string) $stats['equipment']) ?></span>
            <p>Equipment Items</p>
        </article>

        <article class="admin-stat-card">
            <span><?= htmlspecialchars((string) $stats['enrollments']) ?></span>
            <p>Enrollments</p>
        </article>
    </section>
<?php } ?>

<?php
function drawAdminManagementCards(): void { ?>
    <section class="admin-actions">
        <h2>Management</h2>

        <div class="admin-grid">
            <a href="admin_users.php" class="admin-card">
                <h3>Users</h3>
                <p>View users, manage accounts and update roles.</p>
                <span>Manage users →</span>
            </a>

            <a href="admin_trainers.php" class="admin-card">
                <h3>Trainers</h3>
                <p>Add, edit or remove trainer information.</p>
                <span>Manage trainers →</span>
            </a>

            <a href="admin_classes.php" class="admin-card">
                <h3>Classes</h3>
                <p>Create classes, edit schedules and manage capacity.</p>
                <span>Manage classes →</span>
            </a>

            <a href="admin_equipment.php" class="admin-card">
                <h3>Equipment</h3>
                <p>Control equipment quantity and availability.</p>
                <span>Manage equipment →</span>
            </a>
        </div>
    </section>
<?php } ?>

<?php
function drawAdminQuickActions(): void { ?>
    <section class="admin-quick-actions">
        <h2>Quick Actions</h2>

        <div class="quick-actions-row">
            <a href="admin_add_trainer.php" class="quick-action">+ Add Trainer</a>
            <a href="admin_add_class.php" class="quick-action">+ Create Class</a>
            <a href="admin_add_equipment.php" class="quick-action">+ Add Equipment</a>
        </div>
    </section>
<?php } ?>