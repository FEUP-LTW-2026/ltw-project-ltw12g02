<?php
declare(strict_types = 1);

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
    private ?string $plan;
    private ?string $profileImage;

    public function __construct(
        int $user_id,
        string $name,
        string $user_name,
        string $email,
        string $passwordHash,
        string $role,
        ?string $plan,
        ?string $profileImage
    ) {
        $this->user_id = $user_id;
        $this->name = $name;
        $this->user_name = $user_name;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->role = $role;
        $this->plan = $plan ?? 'basic';
        $this->profileImage = $profileImage;
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

    public function getPlan(): string {
        return $this->plan;
    }

    public function getProfileImage(): string {
        return $this->profileImage ?? 'default.png';
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
            $row['Role'],
            $row['Plan'],
            $row['ProfileImage']
        );
    }

    public static function getAllUsers(PDO $db): array {
    $stmt = $db->prepare('
        SELECT *
        FROM Users
        ORDER BY UserId ASC
    ');

    $stmt->execute();

    $users = [];

    while ($row = $stmt->fetch()) {
        $users[] = new Users(
            (int)$row['UserId'],
            $row['Name'],
            $row['Username'],
            $row['Email'],
            $row['PasswordHash'],
            $row['Role'],
            $row['Plan'],
            $row['ProfileImage']
        );
    }

    return $users;
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
            JOIN Enrollments
                ON Enrollments.ClassId = Classes.ClassId
            WHERE Enrollments.UserId = ?
            AND Enrollments.Status = "active"
            ORDER BY datetime(Classes.ClassDateTime) ASC
        ');

        $stmt->execute([$this->user_id]);

        $workoutClasses = [];

        while ($row = $stmt->fetch()) {
            $workoutClasses[] = new WorkoutClass(
                (int)$row['ClassId'],
                (int)$row['TrainerId'],
                (int)$row['ClassTypeId'],
                (string)$row['ClassDateTime'],
                (int)$row['Capacity']
            );
        }

        return $workoutClasses;
    }

    public function getWorkoutClassHistory(PDO $db): array {
        $stmt = $db->prepare('
            SELECT Classes.*
            FROM Classes
            JOIN Enrollments
                ON Enrollments.ClassId = Classes.ClassId
            WHERE Enrollments.UserId = ?
            AND Enrollments.Status = "active"
            AND datetime(Classes.ClassDateTime) < datetime("now", "localtime")
            ORDER BY datetime(Classes.ClassDateTime) DESC
        ');

        $stmt->execute([$this->user_id]);

        $workoutClasses = [];

        while ($row = $stmt->fetch()) {
            $workoutClasses[] = new WorkoutClass(
                (int)$row['ClassId'],
                (int)$row['TrainerId'],
                (int)$row['ClassTypeId'],
                (string)$row['ClassDateTime'],
                (int)$row['Capacity']
            );
        }

        return $workoutClasses;
    }

    public function getWorkoutNextClasses(PDO $db): array {
        $stmt = $db->prepare('
            SELECT Classes.*
            FROM Classes
            JOIN Enrollments
                ON Enrollments.ClassId = Classes.ClassId
            WHERE Enrollments.UserId = ?
            AND Enrollments.Status = "active"
            AND datetime(Classes.ClassDateTime) >= datetime("now", "localtime")
            ORDER BY datetime(Classes.ClassDateTime) ASC
        ');

        $stmt->execute([$this->user_id]);

        $workoutClasses = [];

        while ($row = $stmt->fetch()) {
            $workoutClasses[] = new WorkoutClass(
                (int)$row['ClassId'],
                (int)$row['TrainerId'],
                (int)$row['ClassTypeId'],
                (string)$row['ClassDateTime'],
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
            $row['Role'],
            $row['Plan'],
            $row['ProfileImage']
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

    public static function usernameExistsForOtherUser(PDO $db, string $username, int $userId): bool {
        $stmt = $db->prepare('
            SELECT UserId
            FROM Users
            WHERE Username = ?
              AND UserId != ?
        ');

        $stmt->execute([
            $username,
            $userId
        ]);

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
            INSERT INTO Users (Name, Username, Email, PasswordHash, Role, ProfileImage)
            VALUES (?, ?, ?, ?, ?, ?)
        ');

        $stmt->execute([
            $name,
            $username,
            $email,
            $passwordHash,
            'member',
            null
        ]);

        $id = (int)$db->lastInsertId();

        return new Users(
            $id,
            $name,
            $username,
            $email,
            $passwordHash,
            'member',
            null
        );
    }

    public function updateProfileData(
        PDO $db,
        string $name,
        string $username,
        string $passwordHash
    ): void {
        $stmt = $db->prepare('
            UPDATE Users
            SET Name = ?,
                Username = ?,
                PasswordHash = ?
            WHERE UserId = ?
        ');

        $stmt->execute([
            $name,
            $username,
            $passwordHash,
            $this->user_id
        ]);

        $this->name = $name;
        $this->user_name = $username;
        $this->passwordHash = $passwordHash;
    }

    public function updateProfileImage(PDO $db, string $profileImage): void {
        $stmt = $db->prepare('
            UPDATE Users
            SET ProfileImage = ?
            WHERE UserId = ?
        ');

        $stmt->execute([
            $profileImage,
            $this->user_id
        ]);

        $this->profileImage = $profileImage;
    }

    public static function searchUsers(PDO $db, string $query): array {
        $stmt = $db->prepare('
            SELECT UserId, Name, Username, Email, Role, ProfileImage
            FROM Users
            WHERE Name LIKE ?
            OR Username LIKE ?
            OR Email LIKE ?
            ORDER BY Name ASC
            LIMIT 20
        ');

        $search = $query . '%';

        $stmt->execute([$search, $search, $search]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public function changeRole(string $role, PDO $db): void {
    $allowedRoles = ['member', 'trainer', 'admin'];

    if (!in_array($role, $allowedRoles, true)) {
        throw new InvalidArgumentException('Invalid role.');
    }

    $oldRole = $this->role;

    $stmt = $db->prepare('
        UPDATE Users 
        SET Role = ? 
        WHERE UserId = ?
    ');

    $stmt->execute([
        $role,
        $this->user_id
    ]);

    $this->role = $role;

    if ($oldRole !== 'trainer' && $role === 'trainer') {
        Trainers::createTrainer($this->user_id, $db);
    }

    if ($oldRole === 'trainer' && $role !== 'trainer') {
        Trainers::deleteTrainer($this->user_id, $db);
    }

    if ($role !== 'member' && this->$plan !== 'premium') {
        changePlan('premium', $db);
    }
}

public function changePlan(string $plan, PDO $db): void {
    $allowedPlans = ['basic', 'plus', 'premium'];

    if (!in_array($plan, $allowedPlans, true)) {
        throw new InvalidArgumentException('Invalid plan.');
    }

    $stmt = $db->prepare('
        UPDATE Users 
        SET Plan = ? 
        WHERE UserId = ?
    ');

    $stmt->execute([
        $plan,
        $this->user_id
    ]);

    $this->plan = $plan;
}

public function deleteUser(PDO $db): void {
    try {
        $db->beginTransaction();

        $stmt = $db->prepare('
            DELETE FROM Enrollments
            WHERE UserId = ?
        ');

        $stmt->execute([$this->user_id]);

        $stmt = $db->prepare('
            DELETE FROM Trainers
            WHERE UserId = ?
        ');

        $stmt->execute([$this->user_id]);

        $stmt = $db->prepare('
            DELETE FROM Users
            WHERE UserId = ?
        ');

        $stmt->execute([$this->user_id]);

        $db->commit();

    } catch (PDOException $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        throw $e;
    }
}

public static function searchTrainerCandidates(PDO $db, string $query): array {
    $stmt = $db->prepare('
        SELECT UserId, Name, Username, Email, Role, ProfileImage
        FROM Users
        WHERE Role = ?
          AND (
              Name LIKE ?
              OR Username LIKE ?
              OR Email LIKE ?
          )
        ORDER BY Name ASC
        LIMIT 20
    ');

    $search = $query . '%';

    $stmt->execute([
        'member',
        $search,
        $search,
        $search
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function updateUser(
    PDO $db,
    string $name,
    string $username,
    string $email,
    ?string $password,
    string $role
): void {
    $allowedRoles = ['member', 'trainer', 'admin'];

    if (!in_array($role, $allowedRoles, true)) {
        throw new InvalidArgumentException('Invalid role.');
    }

    try {
        $db->beginTransaction();

        $passwordHash = $this->passwordHash;

        if ($password !== null && $password !== '') {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        }

        $stmt = $db->prepare('
            UPDATE Users
            SET Name = ?,
                Username = ?,
                Email = ?,
                PasswordHash = ?
            WHERE UserId = ?
        ');

        $stmt->execute([
            $name,
            $username,
            $email,
            $passwordHash,
            $this->user_id
        ]);

        $this->name = $name;
        $this->user_name = $username;
        $this->email = $email;
        $this->passwordHash = $passwordHash;

        if ($role !== $this->role) {
            $this->changeRole($role, $db);
        }

        $db->commit();

    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        throw $e;
    }
}

}


?>