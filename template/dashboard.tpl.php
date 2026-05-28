<?php
declare(strict_types = 1);

function drawAdminDashboard(string $adminName, array $stats): void { ?>
    <main class="dashboard-page admin-dashboard">

        <section class="admin-top">
            <div class="page-wrap admin-wrap">
                <?php drawAdminHero($adminName); ?>
                <?php drawAdminStats($stats); ?>
            </div>
        </section>

        <section class="admin-bottom">
            <div class="page-wrap admin-wrap">
                <?php drawAdminManagementCards(); ?>
            </div>
        </section>

    </main>
<?php } ?>


<?php
function drawAdminHero(string $adminName): void { ?>
    <section class="dashboard-hero admin-hero">
        <div>
            <p class="admin-label">PowerPIT Admin Console</p>
            <h1>Welcome back,<br><?= htmlspecialchars($adminName) ?></h1>
            <p>Manage users, trainers, classes and gym equipment from one control panel.</p>
        </div>

        <div class="admin-access-card">
            <span>Access level</span>
            <strong>ADMIN</strong>
            <p>Full control enabled</p>
        </div>
    </section>
<?php } ?>


<?php
function drawAdminStats(array $stats): void { ?>
    <section class="stats-grid admin-stats">
        <?php drawAdminStatCard('Users', (int) ($stats['users'] ?? 0), 'fa-users'); ?>
        <?php drawAdminStatCard('Trainers', (int) ($stats['trainers'] ?? 0), 'fa-id-badge'); ?>
        <?php drawAdminStatCard('Classes', (int) ($stats['classes'] ?? 0), 'fa-calendar'); ?>
        <?php drawAdminStatCard('Equipment', (int) ($stats['equipment'] ?? 0), 'fa-th'); ?>
        <?php drawAdminStatCard('Enrollments', (int) ($stats['enrollments'] ?? 0), 'fa-check-square-o'); ?>
    </section>
<?php } ?>


<?php
function drawAdminStatCard(string $label, int $value, string $icon): void { ?>
    <article class="stat-card admin-stat-card admin-dark-card">
        <div class="admin-card-row">
            <i class="fa <?= htmlspecialchars($icon) ?>" aria-hidden="true"></i>
            <span><?= htmlspecialchars($label) ?></span>
        </div>

        <strong><?= htmlspecialchars((string) $value) ?></strong>
        <p>Registered in system</p>
    </article>
<?php } ?>


<?php
function drawAdminManagementCards(): void { ?>
    <section class="admin-actions">
        <header class="admin-section-header">
            <div>
                <p class="admin-label">Control Center</p>
                <h2>Management</h2>
            </div>

            <p>Choose the area you want to manage.</p>
        </header>

        <div class="admin-grid">
            <?php drawAdminCard(
                'Users',
                'View accounts, search users and update platform roles.',
                'admin_users.php',
                'Manage users',
                'fa-users',
                'Accounts'
            ); ?>

            <?php drawAdminCard(
                'Trainers',
                'Add, edit or remove trainer information.',
                'admin_trainers.php',
                'Manage trainers',
                'fa-id-badge',
                'Staff'
            ); ?>

            <?php drawAdminCard(
                'Classes',
                'Create classes, edit schedules and manage capacity.',
                'admin_classes.php',
                'Manage classes',
                'fa-calendar',
                'Schedule'
            ); ?>

            <?php drawAdminCard(
                'Equipment',
                'Control equipment quantity and availability.',
                'admin_equipment.php',
                'Manage equipment',
                'fa-th',
                'Inventory'
            ); ?>
        </div>
    </section>
<?php } ?>


<?php
function drawAdminCard(
    string $title,
    string $description,
    string $link,
    string $action,
    string $icon,
    string $tag
): void { ?>
    <a href="<?= htmlspecialchars($link) ?>" class="action-card admin-card">
        <div class="admin-card-row">
            <div class="icon-box admin-icon-box">
                <i class="fa <?= htmlspecialchars($icon) ?>" aria-hidden="true"></i>
            </div>

            <span class="pill admin-tag"><?= htmlspecialchars($tag) ?></span>
        </div>

        <div>
            <h3><?= htmlspecialchars($title) ?></h3>
            <p><?= htmlspecialchars($description) ?></p>
        </div>

        <strong><?= htmlspecialchars($action) ?> →</strong>
    </a>
<?php } ?>