<?php
declare(strict_types = 1);

function analyticsFormatDate(?string $dateTime): string {
    if ($dateTime === null || trim($dateTime) === '') {
        return 'Unknown date';
    }

    $timestamp = strtotime($dateTime);

    if ($timestamp === false) {
        return $dateTime;
    }

    return date('d M Y · H:i', $timestamp);
}

function analyticsClampPercentage(int $percentage): int {
    return max(0, min(100, $percentage));
}

function analyticsStatusClass(string $status): string {
    return strtolower(str_replace(' ', '-', $status));
}

function analyticsRenderStars(float $rating): string {
    if ($rating <= 0) {
        return '<span class="analytics-no-rating">No rating</span>';
    }

    $roundedRating = (int) round($rating);
    $html = '<span class="rating-display">';

    for ($i = 1; $i <= 5; $i++) {
        $filled = $i <= $roundedRating ? 'fill' : '';
        $html .= "<span class='star $filled'>★</span>";
    }

    $html .= '</span>';

    return $html;
}

function drawAnalyticsProgressBar(int $percentage): void {
    $percentage = analyticsClampPercentage($percentage);
?>
    <div class="analytics-progress">
        <span style="width: <?= htmlspecialchars((string)$percentage) ?>%"></span>
    </div>
<?php }

function drawAnalyticsStatCard(
    string $iconClass,
    string $label,
    string $value,
    string $description
): void { ?>
    <article class="stat-card trainer_analytics_stat">
        <i class="fa <?= htmlspecialchars($iconClass) ?>" aria-hidden="true"></i>

        <span><?= htmlspecialchars($label) ?></span>

        <strong><?= htmlspecialchars($value) ?></strong>

        <p><?= htmlspecialchars($description) ?></p>
    </article>
<?php }

function drawAnalyticsInsightCard(
    string $label,
    ?array $class,
    string $emptyTitle,
    string $metric
): void { ?>
    <article class="summary-card card analytics-insight-card">
        <p class="admin-label"><?= htmlspecialchars($label) ?></p>

        <?php if ($class === null) { ?>
            <h2><?= htmlspecialchars($emptyTitle) ?></h2>
            <span>No data available yet.</span>
        <?php } else { ?>
            <h2><?= htmlspecialchars((string)$class['ClassName']) ?></h2>

            <span>
                <?= htmlspecialchars(analyticsFormatDate((string)$class['ClassDateTime'])) ?>
            </span>

            <strong><?= htmlspecialchars($metric) ?></strong>
        <?php } ?>
    </article>
<?php }

function drawTrainerAnalyticsFilters(string $filter): void { ?>
    <nav class="admin-class-filters trainer_analytics_filters" aria-label="Trainer analytics filters">
        <a
            href="trainer_analytics.php?filter=upcoming"
            class="pill admin-user-role <?= $filter === 'upcoming' ? 'active' : '' ?>"
        >
            Upcoming
        </a>

        <a
            href="trainer_analytics.php?filter=past"
            class="pill admin-user-role <?= $filter === 'past' ? 'active' : '' ?>"
        >
            Past
        </a>

        <a
            href="trainer_analytics.php?filter=all"
            class="pill admin-user-role <?= $filter === 'all' ? 'active' : '' ?>"
        >
            All classes
        </a>
    </nav>
<?php }

function drawTrainerAnalyticsPage(Users $user, array $overview, array $classPerformance, array $recentReviews, array $personalStats, array $insights, string $filter): void {
    $totalClasses = (int)($overview['TotalClasses'] ?? 0);
    $upcomingClasses = (int)($overview['UpcomingClasses'] ?? 0);
    $pastClasses = (int)($overview['PastClasses'] ?? 0);
    $totalEnrollments = (int)($overview['TotalEnrollments'] ?? 0);
    $occupancyRate = (int)($overview['OccupancyRate'] ?? 0);
    $averageRating = (float)($overview['AverageRating'] ?? 0);
    $totalReviews = (int)($overview['TotalReviews'] ?? 0);
    $totalPersonalClasses = (int)($overview['TotalPersonalClasses'] ?? 0);

    $pendingPersonalClasses = (int)($personalStats['Pending'] ?? 0);
    $acceptedPersonalClasses = (int)($personalStats['Accepted'] ?? 0);
    $rejectedPersonalClasses = (int)($personalStats['Rejected'] ?? 0);
    $upcomingAcceptedPersonalClasses = (int)($personalStats['UpcomingAccepted'] ?? 0);

    $hasFilteredClassData = !empty($classPerformance);
    $hasClassData = $totalClasses > 0;
    $hasReviewData = !empty($recentReviews);
    $hasPersonalData = $totalPersonalClasses > 0;
    $hasAnyData = $hasClassData || $hasReviewData || $hasPersonalData;
?>
    <main class="analytics-page trainer_analytics_page">
        <section class="trainer_analytics_top">
            <div class="page-wrap trainer_analytics_wrap">
                <section class="dashboard-hero trainer_analytics_hero">
                    <div>
                        <p class="admin-label">PowerPIT Trainer Analytics</p>

                        <h1>
                            Welcome back,<br>
                            <?= htmlspecialchars($user->getName()) ?>
                        </h1>

                        <p>
                            Track your class performance, member engagement, ratings
                            and personal training requests in one place.
                        </p>
                    </div>

                    <aside class="trainer_analytics_hero_card">
                        <span>Total assigned classes</span>
                        <strong><?= htmlspecialchars((string)$totalClasses) ?></strong>
                        <p>
                            <?= htmlspecialchars((string)$upcomingClasses) ?>
                            upcoming classes
                        </p>
                    </aside>
                </section>

                <section class="stats-grid trainer_analytics_stats">
                    <?php
                        drawAnalyticsStatCard(
                            'fa-calendar-check-o',
                            'Classes',
                            (string)$totalClasses,
                            $pastClasses . ' completed'
                        );

                        drawAnalyticsStatCard(
                            'fa-users',
                            'Enrollments',
                            (string)$totalEnrollments,
                            'Active member bookings'
                        );

                        drawAnalyticsStatCard(
                            'fa-line-chart',
                            'Occupancy',
                            $occupancyRate . '%',
                            'Based on class capacity'
                        );

                        drawAnalyticsStatCard(
                            'fa-star',
                            'Average Rating',
                            number_format($averageRating, 1) . ' / 5',
                            $totalReviews . ' member reviews'
                        );

                        drawAnalyticsStatCard(
                            'fa-comments',
                            'Personal Classes',
                            (string)$totalPersonalClasses,
                            $pendingPersonalClasses . ' pending requests'
                        );
                    ?>
                </section>
            </div>
        </section>

        <section class="trainer_analytics_bottom">
            <div class="page-wrap trainer_analytics_wrap">
                <?php if ($hasClassData) { ?>
                    <?php drawTrainerAnalyticsFilters($filter); ?>
                <?php } ?>
                <?php if (!$hasAnyData) { ?>
                    <section class="card trainer_analytics_empty_dashboard">
                        <div class="icon-box trainer_analytics_empty_icon">
                            <i class="fa fa-line-chart" aria-hidden="true"></i>
                        </div>

                        <div>
                            <p class="admin-label">No analytics yet</p>

                            <h2>Your dashboard is waiting for activity</h2>

                            <p>
                                Once you are assigned classes, receive bookings, reviews
                                or personal class requests, your analytics will appear here.
                            </p>

                            <div class="trainer_analytics_empty_actions">
                                <a href="profile.php" class="btn small light">
                                    Go to Profile
                                </a>

                                <a href="classes.php" class="btn small">
                                    View Classes
                                </a>
                            </div>
                        </div>
                    </section>
                <?php } else { ?>
                    <section class="trainer_analytics_grid">
                        <article class="section-card card analytics-card analytics-main-card">
                            <header class="analytics-header">
                                <div>
                                    <p class="admin-label">Class Performance</p>
                                    <h2>Attendance and ratings by class</h2>
                                </div>

                                <span class="pill analytics-count-pill">
                                    <?= htmlspecialchars((string)count($classPerformance)) ?> classes
                                </span>
                            </header>

                            <?php if (!$hasFilteredClassData) { ?>
                                <div class="analytics-empty">
                                    <p>No classes found for this filter.</p>
                                </div>
                            <?php } else { ?>
                                <div class="analytics-table-wrap">
                                    <table class="analytics-table">
                                        <thead>
                                            <tr>
                                                <th>Class</th>
                                                <th>Date</th>
                                                <th>Members</th>
                                                <th>Occupancy</th>
                                                <th>Rating</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php foreach ($classPerformance as $class) {
                                                $className = (string)($class['ClassName'] ?? 'Unknown class');
                                                $classDateTime = (string)($class['ClassDateTime'] ?? '');
                                                $capacity = (int)($class['Capacity'] ?? 0);
                                                $enrolled = (int)($class['Enrolled'] ?? 0);
                                                $percentage = (int)($class['OccupancyRate'] ?? 0);
                                                $rating = (float)($class['AverageRating'] ?? 0);
                                                $reviews = (int)($class['TotalReviews'] ?? 0);
                                                $statusLabel = (string)($class['StatusLabel'] ?? 'Open');
                                                $statusClass = analyticsStatusClass($statusLabel);
                                            ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= htmlspecialchars($className) ?></strong>
                                                    </td>

                                                    <td>
                                                        <?= htmlspecialchars(analyticsFormatDate($classDateTime)) ?>
                                                    </td>

                                                    <td>
                                                        <?= htmlspecialchars((string)$enrolled) ?>/<?= htmlspecialchars((string)$capacity) ?>
                                                    </td>

                                                    <td>
                                                        <div class="analytics-progress-cell">
                                                            <?php drawAnalyticsProgressBar($percentage); ?>

                                                            <span>
                                                                <?= htmlspecialchars((string)analyticsClampPercentage($percentage)) ?>%
                                                            </span>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <?= analyticsRenderStars($rating) ?>

                                                        <small>
                                                            <?= htmlspecialchars((string)$reviews) ?>
                                                            reviews
                                                        </small>
                                                    </td>

                                                    <td>
                                                        <span class="status-pill analytics-status analytics-status-<?= htmlspecialchars($statusClass) ?>">
                                                            <?= htmlspecialchars($statusLabel) ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php } ?>
                        </article>

                        <aside class="trainer_analytics_side">
                            <article class="section-card card analytics-card">
                                <header class="analytics-header">
                                    <div>
                                        <p class="admin-label">Personal Training</p>
                                        <h2>Request summary</h2>
                                    </div>
                                </header>

                                <dl class="analytics-metric-list">
                                    <div>
                                        <dt>Pending</dt>
                                        <dd><?= htmlspecialchars((string)$pendingPersonalClasses) ?></dd>
                                    </div>

                                    <div>
                                        <dt>Accepted</dt>
                                        <dd><?= htmlspecialchars((string)$acceptedPersonalClasses) ?></dd>
                                    </div>

                                    <div>
                                        <dt>Rejected</dt>
                                        <dd><?= htmlspecialchars((string)$rejectedPersonalClasses) ?></dd>
                                    </div>

                                    <div>
                                        <dt>Upcoming accepted</dt>
                                        <dd><?= htmlspecialchars((string)$upcomingAcceptedPersonalClasses) ?></dd>
                                    </div>
                                </dl>
                            </article>

                            <article class="section-card card analytics-card">
                                <header class="analytics-header">
                                    <div>
                                        <p class="admin-label">Recent Feedback</p>
                                        <h2>Latest reviews</h2>
                                    </div>
                                </header>

                                <?php if (empty($recentReviews)) { ?>
                                    <div class="analytics-empty">
                                        <p>You do not have any reviews yet.</p>
                                    </div>
                                <?php } else { ?>
                                    <ul class="avatar-list analytics-reviews-list">
                                        <?php foreach ($recentReviews as $review) {
                                            $memberName = (string)($review['Name'] ?? 'Unknown member');
                                            $username = (string)($review['Username'] ?? 'member');
                                            $profileImage = (string)($review['ProfileImage'] ?? 'default.png');
                                            $className = (string)($review['ClassName'] ?? 'Class');
                                            $rating = (float)($review['Rating'] ?? 0);

                                            $reviewText = trim((string)($review['Review'] ?? ''));

                                            if ($reviewText === '') {
                                                $reviewText = 'No written comment.';
                                            }
                                        ?>
                                            <li>
                                                <img
                                                    src="../assets/users/<?= htmlspecialchars($profileImage) ?>"
                                                    alt="Member profile picture"
                                                >

                                                <div>
                                                    <strong>
                                                        <?= htmlspecialchars($memberName) ?>
                                                    </strong>

                                                    <?= analyticsRenderStars($rating) ?>

                                                    <span>
                                                        @<?= htmlspecialchars($username) ?>
                                                        · <?= htmlspecialchars($className) ?>
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

                    <?php if ($hasFilteredClassData) { ?>
                        <section class="trainer_analytics_insights">
                            <?php
                                $mostPopular = $insights['MostPopular'] ?? null;
                                $bestRated = $insights['BestRated'] ?? null;
                                $lowestOccupancy = $insights['LowestOccupancy'] ?? null;

                                drawAnalyticsInsightCard(
                                    'Most Popular Class',
                                    $mostPopular,
                                    'No popular class yet',
                                    $mostPopular === null
                                        ? ''
                                        : (string)$mostPopular['Enrolled'] . '/' . (string)$mostPopular['Capacity'] . ' members'
                                );

                                drawAnalyticsInsightCard(
                                    'Best Rated Class',
                                    $bestRated,
                                    'No rated class yet',
                                    $bestRated === null
                                        ? ''
                                        : number_format((float)$bestRated['AverageRating'], 1) . ' / 5 average rating'
                                );

                                drawAnalyticsInsightCard(
                                    'Lowest Occupancy',
                                    $lowestOccupancy,
                                    'No occupancy data yet',
                                    $lowestOccupancy === null
                                        ? ''
                                        : (string)$lowestOccupancy['OccupancyRate'] . '% occupancy'
                                );
                            ?>
                        </section>
                    <?php } ?>
                <?php } ?>
            </div>
        </section>
    </main>
<?php } ?>