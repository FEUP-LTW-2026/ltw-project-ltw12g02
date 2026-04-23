<?php 
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
}
?>