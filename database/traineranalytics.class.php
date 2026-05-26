<?php
declare(strict_types = 1);

class TrainerAnalytics {

    public static function getOverview(PDO $db, int $trainerId): array {
        $stmt = $db->prepare('
            SELECT
                (
                    SELECT COUNT(*)
                    FROM Classes
                    WHERE TrainerId = ?
                ) AS TotalClasses,

                (
                    SELECT COUNT(*)
                    FROM Classes
                    WHERE TrainerId = ?
                    AND datetime(ClassDateTime) >= datetime("now", "localtime")
                ) AS UpcomingClasses,

                (
                    SELECT COUNT(*)
                    FROM Classes
                    WHERE TrainerId = ?
                    AND datetime(ClassDateTime) < datetime("now", "localtime")
                ) AS PastClasses,

                (
                    SELECT COUNT(*)
                    FROM Enrollments
                    JOIN Classes ON Classes.ClassId = Enrollments.ClassId
                    WHERE Classes.TrainerId = ?
                    AND Enrollments.Status = "active"
                ) AS TotalEnrollments,

                (
                    SELECT COALESCE(SUM(Capacity), 0)
                    FROM Classes
                    WHERE TrainerId = ?
                ) AS TotalCapacity,

                (
                    SELECT COALESCE(ROUND(AVG(Enrollments.Rating), 1), 0)
                    FROM Enrollments
                    JOIN Classes ON Classes.ClassId = Enrollments.ClassId
                    WHERE Classes.TrainerId = ?
                    AND Enrollments.Rating IS NOT NULL
                ) AS AverageRating,

                (
                    SELECT COUNT(Enrollments.Rating)
                    FROM Enrollments
                    JOIN Classes ON Classes.ClassId = Enrollments.ClassId
                    WHERE Classes.TrainerId = ?
                    AND Enrollments.Rating IS NOT NULL
                ) AS TotalReviews,

                (
                    SELECT COUNT(*)
                    FROM PersonalClasses
                    WHERE TrainerId = ?
                ) AS TotalPersonalClasses
        ');

        $stmt->execute([
            $trainerId,
            $trainerId,
            $trainerId,
            $trainerId,
            $trainerId,
            $trainerId,
            $trainerId,
            $trainerId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return [
                'TotalClasses' => 0,
                'UpcomingClasses' => 0,
                'PastClasses' => 0,
                'TotalEnrollments' => 0,
                'TotalCapacity' => 0,
                'AverageRating' => 0,
                'TotalReviews' => 0,
                'TotalPersonalClasses' => 0,
                'OccupancyRate' => 0
            ];
        }

        $totalCapacity = (int)$row['TotalCapacity'];
        $totalEnrollments = (int)$row['TotalEnrollments'];

        $row['OccupancyRate'] = $totalCapacity > 0
            ? (int)round(($totalEnrollments / $totalCapacity) * 100)
            : 0;

        return $row;
    }

    public static function getClassPerformance(PDO $db, int $trainerId): array {
        $stmt = $db->prepare('
            SELECT
                Classes.ClassId,
                Classes.ClassDateTime,
                Classes.Capacity,
                ClassType.Name AS ClassName,

                COUNT(
                    CASE 
                        WHEN Enrollments.Status = "active" 
                        THEN 1 
                    END
                ) AS Enrolled,

                COALESCE(ROUND(AVG(Enrollments.Rating), 1), 0) AS AverageRating,

                COUNT(Enrollments.Rating) AS TotalReviews,

                COUNT(
                    CASE 
                        WHEN Enrollments.Review IS NOT NULL 
                        AND TRIM(Enrollments.Review) <> ""
                        THEN 1 
                    END
                ) AS TotalComments

            FROM Classes
            JOIN ClassType
                ON Classes.ClassTypeId = ClassType.ClassTypeId
            LEFT JOIN Enrollments
                ON Classes.ClassId = Enrollments.ClassId
            WHERE Classes.TrainerId = ?
            GROUP BY Classes.ClassId
            ORDER BY datetime(Classes.ClassDateTime) DESC
        ');

        $stmt->execute([$trainerId]);

        $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($classes as &$class) {
            $capacity = (int)$class['Capacity'];
            $enrolled = (int)$class['Enrolled'];

            $class['OccupancyRate'] = $capacity > 0
                ? (int)round(($enrolled / $capacity) * 100)
                : 0;

            $timestamp = strtotime((string)$class['ClassDateTime']);

            if ($timestamp !== false && $timestamp < time()) {
                $class['StatusLabel'] = 'Completed';
            } else if ($capacity > 0 && $enrolled >= $capacity) {
                $class['StatusLabel'] = 'Full';
            } else if ($enrolled === 0) {
                $class['StatusLabel'] = 'Empty';
            } else {
                $class['StatusLabel'] = 'Open';
            }
        }

        return $classes;
    }

    public static function getRecentReviews(PDO $db, int $trainerId, int $limit = 5): array {
        $limit = max(1, $limit);

        $stmt = $db->prepare('
            SELECT
                Enrollments.Rating,
                Enrollments.Review,
                Classes.ClassDateTime,
                ClassType.Name AS ClassName,
                Users.Name,
                Users.Username,
                Users.ProfileImage
            FROM Classes
            JOIN Enrollments
                ON Classes.ClassId = Enrollments.ClassId
            JOIN Users
                ON Enrollments.UserId = Users.UserId
            JOIN ClassType
                ON Classes.ClassTypeId = ClassType.ClassTypeId
            WHERE Classes.TrainerId = ?
            AND Enrollments.Rating IS NOT NULL
            ORDER BY datetime(Classes.ClassDateTime) DESC
            LIMIT ' . $limit . '
        ');

        $stmt->execute([$trainerId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPersonalClassStats(PDO $db, int $trainerId): array {
        $stats = [
            'Total' => 0,
            'Pending' => 0,
            'Accepted' => 0,
            'Rejected' => 0,
            'UpcomingAccepted' => 0
        ];

        $stmt = $db->prepare('
            SELECT Status, COUNT(*) AS Total
            FROM PersonalClasses
            WHERE TrainerId = ?
            GROUP BY Status
        ');

        $stmt->execute([$trainerId]);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $status = strtolower((string)$row['Status']);
            $total = (int)$row['Total'];

            $stats['Total'] += $total;

            if ($status === 'pending') {
                $stats['Pending'] = $total;
            } else if ($status === 'accepted') {
                $stats['Accepted'] = $total;
            } else if ($status === 'rejected') {
                $stats['Rejected'] = $total;
            }
        }

        $stmt = $db->prepare('
            SELECT COUNT(*) AS UpcomingAccepted
            FROM PersonalClasses
            WHERE TrainerId = ?
            AND Status = "accepted"
            AND datetime(StartDateTime) >= datetime("now", "localtime")
        ');

        $stmt->execute([$trainerId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row !== false) {
            $stats['UpcomingAccepted'] = (int)$row['UpcomingAccepted'];
        }

        return $stats;
    }

    public static function getEngagementInsights(PDO $db, int $trainerId): array {
        $classes = TrainerAnalytics::getClassPerformance($db, $trainerId);

        $mostPopular = null;
        $bestRated = null;
        $lowestOccupancy = null;

        foreach ($classes as $class) {
            if (
                $mostPopular === null ||
                (int)$class['Enrolled'] > (int)$mostPopular['Enrolled']
            ) {
                $mostPopular = $class;
            }

            if (
                (int)$class['TotalReviews'] > 0 &&
                (
                    $bestRated === null ||
                    (float)$class['AverageRating'] > (float)$bestRated['AverageRating']
                )
            ) {
                $bestRated = $class;
            }

            if (
                $lowestOccupancy === null ||
                (int)$class['OccupancyRate'] < (int)$lowestOccupancy['OccupancyRate']
            ) {
                $lowestOccupancy = $class;
            }
        }

        return [
            'MostPopular' => $mostPopular,
            'BestRated' => $bestRated,
            'LowestOccupancy' => $lowestOccupancy
        ];
    }
}
?>