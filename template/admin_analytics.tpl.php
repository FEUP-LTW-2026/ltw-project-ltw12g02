<?php
declare(strict_types = 1);

function adminAnalyticsFormatStars(int $rating): string {
    $rating = max(1, min(5, $rating));

    $html = '<span class="admin_analytics_stars">';

    for ($i = 1; $i <= 5; $i++) {
        $class = $i <= $rating ? 'filled' : '';
        $html .= '<span class="' . $class . '">★</span>';
    }

    $html .= '</span>';

    return $html;
}

function drawAdminAnalyticsProgressBar(int $percentage): void {
    $percentage = max(0, min(100, $percentage));
?>
    <div class="analytics-progress">
        <span style="width: <?= htmlspecialchars((string)$percentage) ?>%"></span>
    </div>
<?php }

function drawAdminAnalyticsStatCard(
    string $iconClass,
    string $label,
    string $value,
    string $description
): void { ?>
    <article class="trainer_analytics_stat">
        <i class="fa <?= htmlspecialchars($iconClass) ?>" aria-hidden="true"></i>

        <span><?= htmlspecialchars($label) ?></span>

        <strong><?= htmlspecialchars($value) ?></strong>

        <p><?= htmlspecialchars($description) ?></p>
    </article>
<?php }

function drawAdminAnalyticsInsightCard(
    string $label,
    string $title,
    string $description
): void { ?>
    <article class="card analytics-insight-card">
        <p class="admin-label"><?= htmlspecialchars($label) ?></p>

        <h2><?= htmlspecialchars($title) ?></h2>

        <span><?= htmlspecialchars($description) ?></span>
    </article>
<?php }

function drawAdminAnalyticsPage(
    string $adminName,
    array $overview,
    array $popularClasses,
    array $equipmentUsage,
    array $recentReviews
): void {
    $totalMembers = (int)($overview['TotalMembers'] ?? 0);
    $totalEnrollments = (int)($overview['TotalEnrollments'] ?? 0);
    $equipmentReservations = (int)($overview['TotalEquipmentReservations'] ?? 0);
    $averageRating = (float)($overview['AverageRating'] ?? 0);
    $occupancyRate = (int)($overview['OccupancyRate'] ?? 0);

    $topClass = $popularClasses[0] ?? null;
    $topEquipment = $equipmentUsage[0] ?? null;

    $topClassTitle = $topClass === null ? 'No class data yet' : (string)$topClass['ClassName'];
    $topClassDescription = $topClass === null
        ? 'No bookings have been registered yet.'
        : (string)$topClass['TotalBookings'] . ' bookings across ' . (string)$topClass['TotalClasses'] . ' classes.';

    $topEquipmentTitle = $topEquipment === null ? 'No equipment data yet' : (string)$topEquipment['Name'];
    $topEquipmentDescription = $topEquipment === null
        ? 'No equipment has been reserved yet.'
        : (string)$topEquipment['TotalReservations'] . ' reservations registered.';

    $engagementDescription = $totalEnrollments . ' class bookings and ' . $equipmentReservations . ' equipment reservations.';
?>
    <main class="trainer_analytics_page admin_analytics_page">
        <section class="trainer_analytics_top">
            <div class="trainer_analytics_wrap">
                <section class="trainer_analytics_hero">
                    <div>
                        <p class="admin-label">PowerPIT Admin Analytics</p>

                        <h1>
                            Gym-wide<br>
                            performance
                        </h1>

                        <p>
                            Welcome back, <?= htmlspecialchars($adminName) ?>.
                            Track demand, ratings, class occupancy and equipment usage.
                        </p>
                    </div>

                    <aside class="trainer_analytics_hero_card">
                        <span>Class occupancy</span>
                        <strong><?= htmlspecialchars((string)$occupancyRate) ?>%</strong>
                        <p>Average booking usage</p>
                    </aside>
                </section>

                <section class="trainer_analytics_stats">
                    <?php
                        drawAdminAnalyticsStatCard(
                            'fa-users',
                            'Members',
                            (string)$totalMembers,
                            'Registered members'
                        );

                        drawAdminAnalyticsStatCard(
                            'fa-check-square-o',
                            'Bookings',
                            (string)$totalEnrollments,
                            'Active class enrollments'
                        );

                        drawAdminAnalyticsStatCard(
                            'fa-star',
                            'Average Rating',
                            number_format($averageRating, 1) . ' / 5',
                            'Average class rating'
                        );

                        drawAdminAnalyticsStatCard(
                            'fa-th',
                            'Equipment Usage',
                            (string)$equipmentReservations,
                            'Total reservations'
                        );
                    ?>
                </section>
            </div>
        </section>

        <section class="trainer_analytics_bottom">
            <div class="trainer_analytics_wrap">

                <section class="trainer_analytics_insights admin_analytics_insights">
                    <?php
                        drawAdminAnalyticsInsightCard(
                            'Most Popular Class',
                            $topClassTitle,
                            $topClassDescription
                        );

                        drawAdminAnalyticsInsightCard(
                            'Most Used Equipment',
                            $topEquipmentTitle,
                            $topEquipmentDescription
                        );

                        drawAdminAnalyticsInsightCard(
                            'Gym Engagement',
                            (string)$totalEnrollments . ' bookings',
                            $engagementDescription
                        );
                    ?>
                </section>

                <section class="trainer_analytics_grid admin_analytics_grid">
                    <article class="card analytics-card analytics-main-card">
                        <header class="analytics-header">
                            <div>
                                <p class="admin-label">Class Demand</p>
                                <h2>Popular classes</h2>
                            </div>

                            <span class="analytics-count-pill">
                                <?= htmlspecialchars((string)count($popularClasses)) ?> types
                            </span>
                        </header>

                        <?php if (empty($popularClasses)) { ?>
                            <div class="analytics-empty">
                                <p>No class analytics available yet.</p>
                            </div>
                        <?php } else { ?>
                            <div class="admin_analytics_list">
                                <?php foreach ($popularClasses as $class) {
                                    $bookings = (int)$class['TotalBookings'];
                                    $capacity = (int)$class['TotalCapacity'];
                                    $percentage = (int)$class['OccupancyRate'];
                                ?>
                                    <section class="admin_analytics_item">
                                        <div>
                                            <strong><?= htmlspecialchars((string)$class['ClassName']) ?></strong>

                                            <span>
                                                <?= htmlspecialchars((string)$class['TotalClasses']) ?> classes
                                                · <?= htmlspecialchars((string)$bookings) ?>/<?= htmlspecialchars((string)$capacity) ?> bookings
                                            </span>
                                        </div>

                                        <div class="admin_analytics_progress_cell">
                                            <?php drawAdminAnalyticsProgressBar($percentage); ?>
                                            <strong><?= htmlspecialchars((string)$percentage) ?>%</strong>
                                        </div>
                                    </section>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </article>

                    <aside class="trainer_analytics_side">
                        <article class="card analytics-card">
                            <header class="analytics-header">
                                <div>
                                    <p class="admin-label">Equipment Usage</p>
                                    <h2>Reserved equipment</h2>
                                </div>

                                <span class="analytics-count-pill">
                                    <?= htmlspecialchars((string)count($equipmentUsage)) ?> items
                                </span>
                            </header>

                            <?php if (empty($equipmentUsage)) { ?>
                                <div class="analytics-empty">
                                    <p>No equipment has been reserved yet.</p>
                                </div>
                            <?php } else { ?>
                                <div class="admin_analytics_list compact">
                                    <?php foreach ($equipmentUsage as $equipment) { ?>
                                        <section class="admin_analytics_item equipment">
                                            <div>
                                                <strong><?= htmlspecialchars((string)$equipment['Name']) ?></strong>

                                                <span>
                                                    <?= htmlspecialchars((string)$equipment['Type']) ?>
                                                    · <?= htmlspecialchars((string)$equipment['Quantity']) ?> units
                                                </span>
                                            </div>

                                            <strong class="admin_analytics_pill">
                                                <?= htmlspecialchars((string)$equipment['TotalReservations']) ?>
                                            </strong>
                                        </section>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </article>

                        <article class="card analytics-card">
                            <header class="analytics-header">
                                <div>
                                    <p class="admin-label">Feedback</p>
                                    <h2>Recent reviews</h2>
                                </div>
                            </header>

                            <?php if (empty($recentReviews)) { ?>
                                <div class="analytics-empty">
                                    <p>No reviews available yet.</p>
                                </div>
                            <?php } else { ?>
                                <ul class="analytics-reviews-list admin_analytics_reviews">
                                    <?php foreach ($recentReviews as $review) {
                                        $reviewText = trim((string)($review['Review'] ?? ''));

                                        if ($reviewText === '') {
                                            $reviewText = 'No written comment.';
                                        }

                                        $memberImage = $review['MemberImage'] ?? 'default.png';

                                        if ($memberImage === null || trim((string)$memberImage) === '') {
                                            $memberImage = 'default.png';
                                        }
                                    ?>
                                        <li>
                                            <img
                                                src="../assets/users/<?= htmlspecialchars((string)$memberImage) ?>"
                                                alt="Member profile picture"
                                            >

                                            <div>
                                                <strong>
                                                    <?= htmlspecialchars((string)$review['MemberName']) ?>
                                                </strong>

                                                <?= adminAnalyticsFormatStars((int)$review['Rating']) ?>

                                                <span>
                                                    <?= htmlspecialchars((string)$review['ClassName']) ?>
                                                    with
                                                    <?= htmlspecialchars((string)$review['TrainerName']) ?>
                                                </span>

                                                <p><?= htmlspecialchars($reviewText) ?></p>
                                            </div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                        </article>
                    </aside>
                </section>
            </div>
        </section>
    </main>
<?php } ?>