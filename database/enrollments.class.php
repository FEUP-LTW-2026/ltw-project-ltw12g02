<?php
declare(strict_types = 1);

require_once(__DIR__ . '/workoutclass.class.php');

class Enrollments {

    private int $enrollment_id;
    private int $user_id;
    private int $class_id;
    private string $enrollment_date;
    private string $enrollment_status;
    private int $rating;
    private string $review;

    public function __construct(
        int $enrollment_id,
        int $user_id,
        int $class_id,
        string $enrollment_date,
        string $enrollment_status,
        int $rating = -1,
        string $review = ""
    ) {
        $this->enrollment_id = $enrollment_id;
        $this->user_id = $user_id;
        $this->class_id = $class_id;
        $this->enrollment_date = $enrollment_date;
        $this->enrollment_status = $enrollment_status;
        $this->rating = $rating;
        $this->review = $review;
    }

    public function get_enrollment_id(): int {
        return $this->enrollment_id;
    }

    public function get_user_id(): int {
        return $this->user_id;
    }

    public function get_class_id(): int {
        return $this->class_id;
    }

    public function get_enrollment_date(): string {
        return $this->enrollment_date;
    }

    public function get_enrollment_status(): string {
        return $this->enrollment_status;
    }

    public function get_rating(): int {
        return $this->rating;
    }

    public function get_review(): string {
        return $this->review;
    }

    public function is_active(): bool {
        return $this->enrollment_status === 'active';
    }

    public static function getEnrollment(PDO $db, int $id): ?Enrollments {
        $stmt = $db->prepare('
            SELECT *
            FROM Enrollments
            WHERE EnrollmentId = ?
        ');

        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return new Enrollments(
            (int)$row['EnrollmentId'],
            (int)$row['UserId'],
            (int)$row['ClassId'],
            $row['EnrollmentDate'],
            $row['Status'],
            $row['Rating'] !== null ? (int)$row['Rating'] : -1,
            $row['Review'] ?? ''
        );
    }

    public static function getEnrollmentByUserAndClass(PDO $db, int $userId, int $classId): ?Enrollments {
        $stmt = $db->prepare('
            SELECT *
            FROM Enrollments
            WHERE UserId = ? AND ClassId = ?
        ');

        $stmt->execute([$userId, $classId]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return new Enrollments(
            (int)$row['EnrollmentId'],
            (int)$row['UserId'],
            (int)$row['ClassId'],
            $row['EnrollmentDate'],
            $row['Status'],
            $row['Rating'] !== null ? (int)$row['Rating'] : -1,
            $row['Review'] ?? ''
        );
    }

    public static function getUserEnrollments(PDO $db, int $userId): array {
        $stmt = $db->prepare('
            SELECT *
            FROM Enrollments
            WHERE UserId = ?
            ORDER BY EnrollmentDate DESC
        ');

        $stmt->execute([$userId]);

        $enrollments = [];

        while ($row = $stmt->fetch()) {
            $enrollments[] = new Enrollments(
                (int)$row['EnrollmentId'],
                (int)$row['UserId'],
                (int)$row['ClassId'],
                $row['EnrollmentDate'],
                $row['Status'],
                $row['Rating'] !== null ? (int)$row['Rating'] : -1,
                $row['Review'] ?? ''
            );
        }

        return $enrollments;
    }

    public static function userHasActiveEnrollment(PDO $db, int $userId, int $classId): bool {
        $stmt = $db->prepare('
            SELECT EnrollmentId
            FROM Enrollments
            WHERE UserId = ?
              AND ClassId = ?
              AND Status = ?
        ');

        $stmt->execute([
            $userId,
            $classId,
            'active'
        ]);

        return $stmt->fetch() !== false;
    }

    public static function addEnrollmentToDb(PDO $db, int $userId, int $classId): void {
        $stmt = $db->prepare('
            INSERT INTO Enrollments (UserId, ClassId, EnrollmentDate, Status)
            VALUES (?, ?, datetime("now"), ?)
        ');

        $stmt->execute([
            $userId,
            $classId,
            'active'
        ]);
    }

    public static function removeEnrollmentFromDb(PDO $db, int $userId, int $classId): bool {
        $stmt = $db->prepare('
            DELETE FROM Enrollments
            WHERE UserId = ? AND ClassId = ?
        ');

        $stmt->execute([
            $userId,
            $classId
        ]);

        return $stmt->rowCount() > 0;
    }

    public static function createEnrollment(PDO $db, int $userId, int $classId): ?Enrollments {
        $class = WorkoutClass::getWorkoutClass($db, $classId);

        if ($class === null) {
            return null;
        }

        if (strtotime($class->getClassDateTime()) < time()) {
            return null;
        }

        if (WorkoutClass::isFull($db, $classId, $class->getCapacity())) {
            return null;
        }

        if (Enrollments::userHasActiveEnrollment($db, $userId, $classId)) {
            return null;
        }

        Enrollments::addEnrollmentToDb($db, $userId, $classId);

        $id = (int)$db->lastInsertId();

        return Enrollments::getEnrollment($db, $id);
    }

    public function cancelEnrollment(PDO $db): bool {
        if (!$this->is_active()) {
            return false;
        }

        $stmt = $db->prepare('
            UPDATE Enrollments
            SET Status = ?
            WHERE EnrollmentId = ?
              AND Status = ?
        ');

        $stmt->execute([
            'cancelled',
            $this->enrollment_id,
            'active'
        ]);

        if ($stmt->rowCount() === 0) {
            return false;
        }

        $this->enrollment_status = 'cancelled';

        return true;
    }

    public static function getMembersByClassId(PDO $db, int $classId): array {
        $stmt = $db->prepare('
            SELECT Users.*
            FROM Enrollments
            JOIN Users ON Users.UserId = Enrollments.UserId
            WHERE Enrollments.ClassId = ?
              AND Enrollments.Status = ?
            ORDER BY Users.Name
        ');

        $stmt->execute([
            $classId,
            'active'
        ]);

        $members = [];

        while ($row = $stmt->fetch()) {
            $members[] = new Users(
                (int)$row['UserId'],
                $row['Name'],
                $row['Username'],
                $row['Email'],
                $row['PasswordHash'],
                $row['Role'],
                $row['ProfileImage']
            );
        }

        return $members;
    }

    public static function updateReview(
        PDO $db,
        int $userId,
        int $classId,
        int $rating,
        string $review
    ): void {
        $stmt = $db->prepare('
            UPDATE Enrollments
            SET Rating = ?,
                Review = ?
            WHERE UserId = ?
              AND ClassId = ?
        ');

        $stmt->execute([
            $rating,
            $review,
            $userId,
            $classId
        ]);
    }
}
?>