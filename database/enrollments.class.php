<?php

class Enrollments {

    private int $enrollment_id;
    private int $user_id;
    private int $class_id;
    private string $enrollment_date;
    private string $enrollment_status;
    private int $rating;
    private int $review;

    public function __construct(
        int $enrollment_id,
        int $user_id,
        int $class_id,
        string $enrollment_date,
        string $enrollment_status
    ) {
        $this->enrollment_id = $enrollment_id;
        $this->user_id = $user_id;
        $this->class_id = $class_id;
        $this->enrollment_date = $enrollment_date;
        $this->enrollment_status = $enrollment_status;
        $this->rating = -1;
        $this->review = "";
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
            (int)$row['Rating'],
            $row['Review']
        );
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
        string $review,
    ): void {
        $stmt = $db->prepare('
            UPDATE ENROLLMENTS
            SET Rating = ?,
                Review = ?
            WHERE UserId = ? AND ClassId = ?
        ');

        $stmt->execute([
            $rating,
            $review,
            $userId,
            $classId
        ]);
    }
}