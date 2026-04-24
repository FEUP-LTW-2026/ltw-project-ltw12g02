<?php
require_once(__DIR__ . '/users.class.php');
require_once(__DIR__ . '/workoutclass.class.php');
require_once(__DIR__ . '/workoutclasstype.class.php');

class Enrollments {

    private int $enrollment_id;
    private int $user_id;
    private int $class_id;
    private string $enrollment_date;

    private string $enrollment_status;

    public function __construct(
        int $enrollment_id,
        int $user_id,
        int $class_id,
        string $enrollment_date
    ) {
        $this->enrollment_id = $enrollment_id;
        $this->user_id = $user_id;
        $this->class_id = $class_id;
        $this->enrollment_date = $enrollment_date;
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
            $row['EnrollmentDate']
        );
    }


    public function is_active(): bool {
        return $this->enrollment_status === 'active';
    }



}