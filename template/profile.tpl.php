<?php
declare(strict_types = 1);

function drawEditProfileDialog(Users $user): void { ?>
    <dialog id="edit-profile-dialog" class="edit-profile-dialog">
        <section class="card edit-profile-card">
            <button 
                type="button" 
                class="edit-profile-close" 
                id="close-edit-profile-dialog"
                aria-label="Close edit profile dialog"
            >
                &times;
            </button>

            <header class="edit-profile-header">
                <p class="profile-member-card-label">PowerPIT Account</p>
                <h1>Edit Profile</h1>
                <p>Update your profile information</p>
            </header>

            <form 
                class="edit-profile-form" 
                action="../actions/action_edit_profile.php" 
                method="post"
                enctype="multipart/form-data"
            >
                <label class="edit-profile-photo" for="profile-image-input">
                    <img 
                        class="profile-image-preview"
                        src="../assets/users/<?= htmlspecialchars($user->getProfileImage()) ?>" 
                        alt="Profile picture"
                    >

                    <span>Change photo</span>
                </label>

                <input 
                    id="profile-image-input"
                    class="edit-profile-file-input"
                    type="file" 
                    name="profile_image"
                    accept="image/png, image/jpeg, image/webp, image/avif"
                >

                <label>
                    Name
                    <input 
                        type="text" 
                        name="name" 
                        value="<?= htmlspecialchars($user->getName()) ?>"
                        required
                    >
                </label>

                <label>
                    Username
                    <input 
                        type="text" 
                        name="username" 
                        value="<?= htmlspecialchars($user->getUserName()) ?>"
                        required
                    >
                </label>

                <section class="edit-profile-password">
                    <h2>Change Password</h2>

                    <label>
                        Current Password
                        <input 
                            type="password" 
                            name="current_password"
                            placeholder="Current password"
                        >
                    </label>

                    <label>
                        New Password
                        <input 
                            type="password" 
                            name="new_password"
                            placeholder="New password"
                        >
                    </label>

                    <label>
                        Confirm New Password
                        <input 
                            type="password" 
                            name="confirm_password"
                            placeholder="Confirm new password"
                        >
                    </label>
                </section>

                <div class="edit-profile-actions">
                    <button 
                        type="button" 
                        class="btn small edit-profile-cancel"
                        id="cancel-edit-profile-dialog"
                    >
                        Cancel
                    </button>

                    <button type="submit" class="btn small light">
                        Save Changes
                    </button>
                </div>
            </form>
        </section>
    </dialog>
<?php } ?>


<?php
function drawProfile(Users $user, array $workoutClasses): void { ?>
    <main>
        <section class="flex-row light">
            <div class="flex-item">
                <div class="card card--dark">
                    <div class="profile-member-card-content">
                        <img 
                            class="profile-image-preview"
                            src="../assets/users/<?= htmlspecialchars($user->getProfileImage()) ?>" 
                            alt="Profile picture" 
                            width="200" 
                            height="100"
                        >

                        <div>
                            <p class="profile-member-card-label">PowerPIT Member</p>
                            <h1><?= htmlspecialchars($user->getName()) ?></h1>
                            <p class="profile-member-card-meta">
                                <?= htmlspecialchars(ucfirst($user->getRole())) ?> Account
                            </p>
                        </div>

                        <button 
                            type="button" 
                            class="btn small light profile-edit-btn"
                            id="open-edit-profile-dialog"
                        >
                            Edit Profile
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid">
            <article class="card">
                <h2 class="card-title center">Personal Information</h2>

                <dl>
                    <div class="card-dl-row">
                        <dt>Username</dt>
                        <dd><?= htmlspecialchars($user->getUserName()) ?></dd>
                    </div>

                    <div class="card-dl-row">
                        <dt>Email</dt>
                        <dd><?= htmlspecialchars($user->getEmail()) ?></dd>
                    </div>

                    <div class="card-dl-row">
                        <dt>Role</dt>
                        <dd><?= htmlspecialchars(ucfirst($user->getRole())) ?></dd>
                    </div>

                    <div class="card-dl-row">
                        <dt>Plan</dt>
                        <dd>Standard</dd>
                    </div>
                </dl>
            </article>

            <aside class="card">
                <h2 class="card-title center">Next Classes</h2>

                <?php if (empty($workoutClasses)) { ?>
                    <p>You do not have any booked classes yet.</p>
                <?php } else { ?>
                    <dl>
                        <?php foreach ($workoutClasses as $workoutClass) { ?>
                            <div class="card-dl-row">
                                <dt>Class #<?= htmlspecialchars((string)$workoutClass->getClassId()) ?></dt>
                                <dd>
                                    <?= htmlspecialchars(date('d M · H:i', strtotime($workoutClass->getClassDateTime()))) ?>
                                </dd>
                            </div>
                        <?php } ?>
                    </dl>
                <?php } ?>
            </aside>
        </section>

        <?php drawEditProfileDialog($user); ?>
    </main>
<?php } ?>