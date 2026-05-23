
<?php
require_once(__DIR__ . '/users.class.php');
require_once(__DIR__ . '/enrollments.class.php');
require_once(__DIR__ . '/workoutclass.class.php');
require_once(__DIR__ . '/workoutclasstype.class.php');


class Trainers {

    private int $trainer_id;
    private int $user_id;
    private ?string $bio;
    private ?string $specializations;
    private ?string $certifications;

    public function __construct(
        int $trainer_id,
        int $user_id,
        ?string $bio,
        ?string $specializations,
        ?string $certifications
    ) {
        $this->trainer_id = $trainer_id;
        $this->user_id = $user_id;
        $this->bio = $bio;
        $this->specializations = $specializations;
        $this->certifications = $certifications;
    }

    public function getTrainerId(): int {
        return $this->trainer_id;
    }

    public function getUserId(): int {
        return $this->user_id;
    }

    public function getBio(): ?string {
        return $this->bio;
    }

    public function getSpecializations(): ?string {
        return $this->specializations;
    }

    public function getCertifications(): ?string {
        return $this->certifications;
    }

    public static function getTrainer(PDO $db, int $id): ?Trainers {
        $stmt = $db->prepare('
            SELECT *
            FROM Trainers
            WHERE TrainerId = ?
        ');

        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return new Trainers(
            (int)$row['TrainerId'], 
            (int)$row['UserId'], 
            $row['Bio'], 
            $row['Specializations'], 
            $row['Certifications']
        );
    }

    public function getUser(PDO $db): ?Users {
        return Users::getUser($db, $this->user_id);
    }

    public static function getAllTrainers(PDO $db): array {
        $stmt = $db->prepare('
            SELECT Trainers.*
            FROM Trainers
            JOIN Users ON Users.UserId = Trainers.UserId
            ORDER BY Users.Name
        ');

        $stmt->execute();

        $trainers = [];

        while ($row = $stmt->fetch()) {
            $trainers[] = new Trainers(
                (int)$row['TrainerId'],
                (int)$row['UserId'],
                $row['Bio'],
                $row['Specializations'],
                $row['Certifications']
            );
        }

        return $trainers;
    }

    public function getName(PDO $db) : string{

        return UserS::getUser($db,$this->user_id)->getName();


    }

    public static function getTrainerByUserId(PDO $db, int $userId): ?Trainers {
    $stmt = $db->prepare('
        SELECT *
        FROM Trainers
        WHERE UserId = ?
    ');

    $stmt->execute([$userId]);

    $row = $stmt->fetch();

    if ($row === false) {
        return null;
    }

    return new Trainers(
        (int)$row['TrainerId'],
        (int)$row['UserId'],
        $row['Bio'],
        $row['Specializations'],
        $row['Certifications']
    );
    }

    public function getAssignedClasses(PDO $db): array {
        $stmt = $db->prepare('
            SELECT *
            FROM Classes
            WHERE TrainerId = ?
            ORDER BY ClassDateTime
        ');

        $stmt->execute([$this->trainer_id]);

        $classes = [];

        while ($row = $stmt->fetch()) {
            $classes[] = new WorkoutClass(
                (int)$row['ClassId'],
                (int)$row['TrainerId'],
                (int)$row['ClassTypeId'],
                $row['ClassDateTime'],
                (int)$row['Capacity']
            );
        }

        return $classes;
    }

    public function getReviews(PDO $db): array {
        $stmt = $db->prepare('
            SELECT
                Enrollments.Rating,
                Enrollments.Review,
                Classes.ClassDateTime,
                ClassType.Name AS ClassType,
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
            ORDER BY Classes.ClassDateTime DESC
        ');

        $stmt->execute([$this->trainer_id]);

        return $stmt->fetchAll();
    }

    public static function searchTrainers(PDO $db, string $query): array {
        $stmt = $db->prepare('
            SELECT *
            FROM Users
                 NATURAL JOIN Trainers
            WHERE (Name LIKE ?
                  OR Username LIKE ?
                  OR Email LIKE ? ) AND Role = ?
            ORDER BY Name ASC
            LIMIT 20
        ');

        $search = $query . '%';

        $stmt->execute([$search, $search, $search, 'trainer']);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAverageRatings(PDO $db): array {
        $stmt = $db->prepare('
            SELECT 
                COALESCE(ROUND(AVG(Enrollments.Rating), 1), 0) AS AverageRating,
                COUNT(Enrollments.Rating) AS TotalReviews
            FROM Classes
            JOIN Enrollments
                ON Classes.ClassId = Enrollments.ClassId
            WHERE Classes.TrainerId = ?
            AND Enrollments.Rating IS NOT NULL;
        ');

        $stmt->execute([$this->trainer_id]);

        return $stmt->fetch();
    }
}