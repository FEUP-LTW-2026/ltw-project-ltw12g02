<?php
declare(strict_types = 1);

function drawProfile(Users $user, array $workoutClasses): void { ?>
    <main>
        <section class="flex-row light">
            <div class="flex-item">
                <div class="card card--dark">
                    <div class="profile-member-card-content">
                        <img 
                            src="https://picsum.photos/600/300" 
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
    </main>
<?php } ?>