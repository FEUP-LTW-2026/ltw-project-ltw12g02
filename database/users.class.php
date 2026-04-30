<?php

require_once(__DIR__ . '/enrollments.class.php');
require_once(__DIR__ . '/workoutclass.class.php');
require_once(__DIR__ . '/workoutclasstype.class.php');
require_once(__DIR__ . '/trainers.class.php');

class Users {

    private int $user_id;
    private string $name;
    private string $user_name;
    private string $email; 
    private string $passwordHash;
    private string $role;

    public function __construct(
        int $user_id,
        string $name,
        string $user_name,
        string $email,
        string $passwordHash,
        string $role
    ) {
        $this->user_id = $user_id;
        $this->name = $name;
        $this->user_name = $user_name;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->role = $role;
    }

    public function getUserId(): int {
        return $this->user_id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getUserName(): string {
        return $this->user_name;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getPasswordHash(): string {
        return $this->passwordHash;
    }

    public function getRole(): string {
        return $this->role;
    }

    public static function getUser(PDO $db, int $id): ?Users {
        $stmt = $db->prepare('
            SELECT *
            FROM Users
            WHERE UserId = ?
        ');

        $stmt->execute([$id]);

        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return new Users(
            (int)$row['UserId'],
            $row['Name'],
            $row['Username'],
            $row['Email'],
            $row['PasswordHash'],
            $row['Role']
        );
    }

    public function getEnrollments(PDO $db): array {
        $stmt = $db->prepare('
            SELECT *
            FROM Enrollments
            WHERE UserId = ?
        ');

        $stmt->execute([$this->user_id]);

        $enrollments = [];

        while ($row = $stmt->fetch()) {
            $enrollments[] = new Enrollments(
                (int)$row['EnrollmentId'],
                (int)$row['UserId'],
                (int)$row['ClassId'],
                $row['EnrollmentDate'],
                $row['Status']
            );
        }

        return $enrollments;
    }

    public function getWorkoutClasses(PDO $db): array {
    $stmt = $db->prepare('
        SELECT Classes.*
        FROM Classes
        JOIN Enrollments ON Enrollments.ClassId = Classes.ClassId
        WHERE Enrollments.UserId = ?
        ORDER BY Classes.ClassDateTime
    ');

    $stmt->execute([$this->user_id]);

    $workoutClasses = [];

    while ($row = $stmt->fetch()) {
        $workoutClasses[] = new WorkoutClass(
            (int)$row['ClassId'],
            (int)$row['TrainerId'],
            (int)$row['ClassTypeId'],
            $row['ClassDateTime'],
            (int)$row['Capacity']
        );
    }

    return $workoutClasses;
}
}