<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/csrf.php');

require_once(__DIR__ . '/../database/users.class.php');

function adminEditUserRoleLevel(string $role): int {
    return match ($role) {
        'member' => 1,
        'trainer' => 2,
        'admin' => 3,
        default => 0,
    };
}

function adminEditUserRoleActionLabel(string $currentRole, string $targetRole): string {
    if ($currentRole === $targetRole) {
        return match ($targetRole) {
            'member' => 'Already Member',
            'trainer' => 'Already Trainer',
            'admin' => 'Already Admin',
            default => 'Current Role',
        };
    }

    $currentLevel = adminEditUserRoleLevel($currentRole);
    $targetLevel = adminEditUserRoleLevel($targetRole);

    if ($targetLevel > $currentLevel) {
        return match ($targetRole) {
            'trainer' => 'Promote to Trainer',
            'admin' => 'Promote to Admin',
            default => 'Promote',
        };
    }

    if ($targetLevel < $currentLevel) {
        return match ($targetRole) {
            'member' => 'Downgrade to Member',
            'trainer' => 'Downgrade to Trainer',
            default => 'Downgrade',
        };
    }

    return 'Change Role';
}

function adminEditUserRoleCardTitle(string $currentRole, string $targetRole): string {
    if ($currentRole === $targetRole) {
        return match ($targetRole) {
            'member' => 'Current Member Account',
            'trainer' => 'Current Trainer Account',
            'admin' => 'Current Admin Account',
            default => 'Current Account',
        };
    }

    $currentLevel = adminEditUserRoleLevel($currentRole);
    $targetLevel = adminEditUserRoleLevel($targetRole);

    if ($targetLevel > $currentLevel) {
        return match ($targetRole) {
            'trainer' => 'Promote to Trainer',
            'admin' => 'Promote to Admin',
            default => 'Promote User',
        };
    }

    return match ($targetRole) {
        'member' => 'Downgrade to Member',
        'trainer' => 'Downgrade to Trainer',
        default => 'Downgrade User',
    };
}

function drawAdminEditUserRoleCard(
    Users $user,
    string $targetRole,
    string $description,
    string $icon
): void {
    $currentRole = $user->getRole();
    $isCurrentRole = $currentRole === $targetRole;

    $title = adminEditUserRoleCardTitle($currentRole, $targetRole);
    $buttonLabel = adminEditUserRoleActionLabel($currentRole, $targetRole);
    $dialogId = 'admin-role-dialog-' . $targetRole;
    ?>

    <article class="action-card admin-card">
        <div>
            <div class="admin-card-row">
                <span class="pill admin-tag">
                    <?= htmlspecialchars($targetRole) ?>
                </span>

                <div class="icon-box admin-icon-box">
                    <i class="fa <?= htmlspecialchars($icon) ?>"></i>
                </div>
            </div>

            <h3><?= htmlspecialchars($title) ?></h3>

            <p>
                <?= htmlspecialchars($description) ?>
            </p>
        </div>

        <button
            type="button"
            class="btn light <?= $isCurrentRole ? 'disabled' : '' ?>"
            data-confirm-open="<?= htmlspecialchars($dialogId) ?>"
            <?= $isCurrentRole ? 'disabled' : '' ?>
        >
            <?= htmlspecialchars($buttonLabel) ?>
        </button>

        <dialog
            id="<?= htmlspecialchars($dialogId) ?>"
            class="modal popup-dialog admin-confirm-dialog"
        >
            <section class="modal-card popup-card admin-confirm-card">
                <button
                    type="button"
                    class="popup-close"
                    data-confirm-close
                >
                    ×
                </button>

                <header class="popup-header">
                    <p class="profile-member-card-label">Confirm Action</p>

                    <h1><?= htmlspecialchars($buttonLabel) ?></h1>

                    <p>
                        This action will change this user's role.
                    </p>
                </header>

                <form
                    action="../actions/action_admin_change_role.php"
                    method="post"
                    class="form-stack popup-form admin-confirm-form"
                    data-confirm-name="<?= htmlspecialchars($user->getName()) ?>"
                >
                    <?php sendCSRF(); ?>
                    <input
                        type="hidden"
                        name="id"
                        value="<?= htmlspecialchars((string) $user->getUserId()) ?>"
                    >

                    <input
                        type="hidden"
                        name="role"
                        value="<?= htmlspecialchars($targetRole) ?>"
                    >

                    <p class="admin-confirm-warning">
                        To confirm this action, type the user's name:
                        <strong><?= htmlspecialchars($user->getName()) ?></strong>
                    </p>

                    <label>
                        User name

                        <input
                            type="text"
                            name="confirmation_name"
                            autocomplete="off"
                            data-confirm-input
                            required
                        >
                    </label>

                    <div class="actions-row popup-actions">
                        <button
                            type="button"
                            class="btn"
                            data-confirm-close
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="btn light"
                            data-confirm-submit
                            disabled
                        >
                            Confirm
                        </button>
                    </div>
                </form>
            </section>
        </dialog>
    </article>

<?php }

function drawAdminEditUserPage(Users $user): void { ?>
    <main class="page-shell admin-page">

        <section class="hero-panel admin-manage-hero">
            <p class="admin-label">Admin Panel</p>

            <h1>User Management</h1>

            <p>
                View this user's account details and manage their permissions inside PowerPIT.
            </p>
        </section>

        <section class="card admin-edit-user-card">

            <header class="admin-section-header">
                <div>
                    <p class="admin-label">User Profile</p>

                    <h2><?= htmlspecialchars($user->getName()) ?></h2>
                </div>

                <p>
                    Current role:
                    <strong><?= htmlspecialchars($user->getRole()) ?></strong>
                </p>
            </header>

            <div class="admin-edit-user-profile">
                <img
                    src="../assets/users/<?= htmlspecialchars($user->getProfileImage()) ?>"
                    alt="<?= htmlspecialchars($user->getName()) ?>"
                    class="admin-edit-user-avatar"
                >

                <div class="admin-edit-user-main-info">
                    <strong>
                        <?= htmlspecialchars($user->getName()) ?>
                    </strong>

                    <p>
                        @<?= htmlspecialchars($user->getUserName()) ?>
                    </p>

                    <p class="admin-user-email">
                        <?= htmlspecialchars($user->getEmail()) ?>
                    </p>
                </div>

                <span class="pill admin-role">
                    <?= htmlspecialchars($user->getRole()) ?>
                </span>
            </div>

            <dl>
                <div class="card-dl-row">
                    <dt>User ID</dt>
                    <dd><?= htmlspecialchars((string) $user->getUserId()) ?></dd>
                </div>

                <div class="card-dl-row">
                    <dt>Name</dt>
                    <dd><?= htmlspecialchars($user->getName()) ?></dd>
                </div>

                <div class="card-dl-row">
                    <dt>Username</dt>
                    <dd>@<?= htmlspecialchars($user->getUserName()) ?></dd>
                </div>

                <div class="card-dl-row">
                    <dt>Email</dt>
                    <dd><?= htmlspecialchars($user->getEmail()) ?></dd>
                </div>

                <div class="card-dl-row">
                    <dt>Role</dt>
                    <dd><?= htmlspecialchars($user->getRole()) ?></dd>
                </div>

                <div class="card-dl-row">
                    <dt>Profile Image</dt>
                    <dd><?= htmlspecialchars($user->getProfileImage()) ?></dd>
                </div>
            </dl>

        </section>

        <section class="card admin-edit-user-section">

            <header class="admin-section-header">
                <div>
                    <p class="admin-label">Permissions</p>

                    <h2>Change User Role</h2>
                </div>

                <p>
                    Promote or downgrade this user by choosing one of the available roles.
                </p>
            </header>

            <div class="admin-grid">
                <?php
                    drawAdminEditUserRoleCard(
                        $user,
                        'member',
                        'Members can book classes, reserve equipment and use the normal gym features.',
                        'fa-user'
                    );

                    drawAdminEditUserRoleCard(
                        $user,
                        'trainer',
                        'Trainers can appear in the trainers page and can be connected to classes and members.',
                        'fa-star'
                    );

                    drawAdminEditUserRoleCard(
                        $user,
                        'admin',
                        'Admins can access the admin panel and manage users, trainers, classes and equipment.',
                        'fa-lock'
                    );
                ?>
            </div>

        </section>

        <section class="card admin-edit-user-section">

            <header class="admin-section-header">
                <div>
                    <p class="admin-label">Account Actions</p>

                    <h2>Manage Account</h2>
                </div>

                <p>
                    Extra actions for this user account.
                </p>
            </header>

            <div class="actions-row popup-actions">
                <a href="../pages/admin_users.php" class="btn">
                    Back to Users
                </a>

                <button
                    type="button"
                    class="btn light"
                    data-confirm-open="delete-user-dialog"
                >
                    Delete User
                </button>
            </div>

            <dialog
                id="delete-user-dialog"
                class="modal popup-dialog admin-confirm-dialog"
            >
                <section class="modal-card popup-card admin-confirm-card">
                    <button
                        type="button"
                        class="popup-close"
                        data-confirm-close
                    >
                        ×
                    </button>

                    <header class="popup-header">
                        <p class="profile-member-card-label">Danger Zone</p>

                        <h1>Delete User</h1>

                        <p>
                            This action may permanently remove this user account.
                        </p>
                    </header>

                    <form
                        action="../actions/action_admin_delete_user.php"
                        method="post"
                        class="form-stack popup-form admin-confirm-form"
                        data-confirm-name="<?= htmlspecialchars($user->getName()) ?>"
                    >
                        <?php sendCSRF(); ?>
                        <input
                            type="hidden"
                            name="id"
                            value="<?= htmlspecialchars((string) $user->getUserId()) ?>"
                        >

                        <p class="admin-confirm-warning danger">
                            To confirm deleting this user, type the user's name:
                            <strong><?= htmlspecialchars($user->getName()) ?></strong>
                        </p>

                        <label>
                            User name

                            <input
                                type="text"
                                name="confirmation_name"
                                autocomplete="off"
                                data-confirm-input
                                required
                            >
                        </label>

                        <div class="actions-row popup-actions">
                            <button
                                type="button"
                                class="btn"
                                data-confirm-close
                            >
                                Cancel
                            </button>

                            <button
                                type="submit"
                                class="btn light"
                                data-confirm-submit
                                disabled
                            >
                                Delete User
                            </button>
                        </div>
                    </form>
                </section>
            </dialog>

        </section>

    </main>
<?php } ?>