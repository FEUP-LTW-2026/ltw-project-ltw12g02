<?php
declare(strict_types = 1);

class AdminAnalytics {

    public static function getOverview(PDO $db): array {
        $stmt = $db->prepare('
            SELECT
                (
                    SELECT COUNT(*)
                    FROM Users
                    WHERE Role = "member"
                ) AS TotalMembers,

                (
                    SELECT COUNT(*)
                    FROM Enrollments
                    WHERE Status = "active"
                ) AS TotalEnrollments,

                (
                    SELECT COUNT(*)
                    FROM EquipmentReservations
                ) AS TotalEquipmentReservations,

                (
                    SELECT COALESCE(ROUND(AVG(Rating), 1), 0)
                    FROM Enrollments
                    WHERE Rating IS NOT NULL
                    AND Rating >= 1
                ) AS AverageRating
        ');

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return [
                'TotalMembers' => 0,
                'TotalEnrollments' => 0,
                'TotalEquipmentReservations' => 0,
                'AverageRating' => 0,
                'OccupancyRate' => 0
            ];
        }

        $stmt = $db->prepare('
            SELECT
                COALESCE(SUM(ClassStats.Enrolled), 0) AS TotalBookings,
                COALESCE(SUM(ClassStats.Capacity), 0) AS TotalCapacity
            FROM (
                SELECT
                    Classes.ClassId,
                    Classes.Capacity,
                    COUNT(
                        CASE
                            WHEN Enrollments.Status = "active"
                            THEN 1
                        END
                    ) AS Enrolled
                FROM Classes
                LEFT JOIN Enrollments
                    ON Enrollments.ClassId = Classes.ClassId
                GROUP BY Classes.ClassId
            ) AS ClassStats
        ');

        $stmt->execute();

        $occupancy = $stmt->fetch(PDO::FETCH_ASSOC);

        $totalBookings = $occupancy === false ? 0 : (int)$occupancy['TotalBookings'];
        $totalCapacity = $occupancy === false ? 0 : (int)$occupancy['TotalCapacity'];

        $row['OccupancyRate'] = $totalCapacity > 0
            ? (int)round(($totalBookings / $totalCapacity) * 100)
            : 0;

        return $row;
    }

    public static function getPopularClasses(PDO $db, int $limit = 6): array {
        $limit = max(1, $limit);

        $stmt = $db->prepare('
            SELECT
                ClassTypeStats.ClassTypeId,
                ClassTypeStats.ClassName,
                COUNT(*) AS TotalClasses,
                SUM(ClassTypeStats.Enrolled) AS TotalBookings,
                SUM(ClassTypeStats.Capacity) AS TotalCapacity,
                COALESCE(ROUND(AVG(ClassTypeStats.AverageRating), 1), 0) AS AverageRating
            FROM (
                SELECT
                    Classes.ClassId,
                    ClassType.ClassTypeId,
                    ClassType.Name AS ClassName,
                    Classes.Capacity,

                    COUNT(
                        CASE
                            WHEN Enrollments.Status = "active"
                            THEN 1
                        END
                    ) AS Enrolled,

                    AVG(
                        CASE
                            WHEN Enrollments.Rating IS NOT NULL
                            AND Enrollments.Rating >= 1
                            THEN Enrollments.Rating
                        END
                    ) AS AverageRating

                FROM Classes
                JOIN ClassType
                    ON ClassType.ClassTypeId = Classes.ClassTypeId
                LEFT JOIN Enrollments
                    ON Enrollments.ClassId = Classes.ClassId
                GROUP BY Classes.ClassId
            ) AS ClassTypeStats
            GROUP BY ClassTypeStats.ClassTypeId
            ORDER BY TotalBookings DESC, TotalClasses DESC
            LIMIT ' . $limit . '
        ');

        $stmt->execute();

        $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($classes as &$class) {
            $totalCapacity = (int)$class['TotalCapacity'];
            $totalBookings = (int)$class['TotalBookings'];

            $class['OccupancyRate'] = $totalCapacity > 0
                ? (int)round(($totalBookings / $totalCapacity) * 100)
                : 0;
        }

        return $classes;
    }

    public static function getEquipmentUsage(PDO $db, int $limit = 6): array {
        $limit = max(1, $limit);

        $stmt = $db->prepare('
            SELECT
                Equipment.EquipmentId,
                Equipment.Name,
                Equipment.Type,
                Equipment.Quantity,

                COUNT(EquipmentReservations.EquipmentReservationId) AS TotalReservations,

                COUNT(
                    CASE
                        WHEN datetime(EquipmentReservations.ReservationDateTime) >= datetime("now", "localtime")
                        THEN 1
                    END
                ) AS UpcomingReservations

            FROM Equipment
            JOIN EquipmentReservations
                ON EquipmentReservations.EquipmentId = Equipment.EquipmentId
            GROUP BY Equipment.EquipmentId
            HAVING COUNT(EquipmentReservations.EquipmentReservationId) > 0
            ORDER BY TotalReservations DESC, Equipment.Name ASC
            LIMIT ' . $limit . '
        ');

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getRecentReviews(PDO $db, int $limit = 5): array {
        $limit = max(1, $limit);

        $stmt = $db->prepare('
            SELECT
                Enrollments.Rating,
                Enrollments.Review,
                Classes.ClassDateTime,
                ClassType.Name AS ClassName,
                Member.Name AS MemberName,
                Member.Username AS MemberUsername,
                Member.ProfileImage AS MemberImage,
                TrainerUser.Name AS TrainerName
            FROM Enrollments
            JOIN Classes
                ON Classes.ClassId = Enrollments.ClassId
            JOIN ClassType
                ON ClassType.ClassTypeId = Classes.ClassTypeId
            JOIN Users AS Member
                ON Member.UserId = Enrollments.UserId
            JOIN Trainers
                ON Trainers.TrainerId = Classes.TrainerId
            JOIN Users AS TrainerUser
                ON TrainerUser.UserId = Trainers.UserId
            WHERE Enrollments.Rating IS NOT NULL
            AND Enrollments.Rating >= 1
            ORDER BY datetime(Classes.ClassDateTime) DESC
            LIMIT ' . $limit . '
        ');

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>