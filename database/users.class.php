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

public static function getUserWithPassword(PDO $db, string $email, string $password): ?Users {
    $stmt = $db->prepare('
        SELECT *
        FROM Users
        WHERE Email = ?
    ');

    $stmt->execute([$email]);

    $row = $stmt->fetch();

    if ($row === false) {
        return null;
    }

    if (!password_verify($password, $row['PasswordHash'])) {
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

public static function emailExists(PDO $db, string $email): bool {
    $stmt = $db->prepare('
        SELECT UserId
        FROM Users
        WHERE Email = ?
    ');

    $stmt->execute([$email]);

    return $stmt->fetch() !== false;
}

public static function usernameExists(PDO $db, string $username): bool {
    $stmt = $db->prepare('
        SELECT UserId
        FROM Users
        WHERE Username = ?
    ');

    $stmt->execute([$username]);

    return $stmt->fetch() !== false;
}

public static function create(
    PDO $db,
    string $name,
    string $username,
    string $email,
    string $password
): ?Users {
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $db->prepare('
        INSERT INTO Users (Name, Username, Email, PasswordHash, Role)
        VALUES (?, ?, ?, ?, ?)
    ');

    $stmt->execute([
        $name,
        $username,
        $email,
        $passwordHash,
        'member'
    ]);

    $id = (int)$db->lastInsertId();

    return new Users(
        $id,
        $name,
        $username,
        $email,
        $passwordHash,
        'member'
    );
}
}