<?php

require_once(__DIR__ . '/enrollments.class.php');
require_once(__DIR__ . '/workoutclasstype.class.php');
require_once(__DIR__ . '/trainers.class.php');

class WorkoutClass {
    private int $id;
    private int $trainerId;
    private int $classTypeId;
    private string $classDateTime;
    private int $capacity;

    public function __construct(
        int $id,
        int $trainerId,
        int $classTypeId,
        string $classDateTime,
        int $capacity
    ) {
        $this->id = $id;
        $this->trainerId = $trainerId;
        $this->classTypeId = $classTypeId;
        $this->classDateTime = $classDateTime;
        $this->capacity = $capacity;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getTrainerId(): int {
        return $this->trainerId;
    }

    public function getClassTypeId(): int {
        return $this->classTypeId;
    }

    public function getClassDateTime(): string {
        return $this->classDateTime;
    }

    public function getCapacity(): int {
        return $this->capacity;
    }

    public static function getWorkoutClass(PDO $db, int $id): ?WorkoutClass {
        $stmt = $db->prepare('
            SELECT *
            FROM Classes
            WHERE ClassId = ?
        ');

        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return new WorkoutClass(
            (int) $row['ClassId'],
            (int) $row['TrainerId'],
            (int) $row['ClassTypeId'],
            (string) $row['ClassDateTime'],
            (int) $row['Capacity']
        );
    }

    public static function getFilteredClasses(
        PDO $db,
        string $classTypeId,
        ?string $trainerId,
        ?string $date,
        ?string $time
    ): array {
        $sql = '
            SELECT *
            FROM Classes
            WHERE ClassDateTime >= datetime()
            AND ClassTypeId = ?
        ';

        $params = [$classTypeId];

        if (!empty($trainerId)) {
            $sql .= ' AND TrainerId = ?';
            $params[] = $trainerId;
        }

        if (!empty($date)) {
            $sql .= ' AND DATE(ClassDateTime) = ?';
            $params[] = $date;
        }

        if (!empty($time)) {
            $sql .= ' AND TIME(ClassDateTime) >= ?';
            $params[] = $time;
        }

        $sql .= '
            ORDER BY ClassDateTime ASC
        ';

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        $classes = [];

        while ($row = $stmt->fetch()) {
            $classes[] = new WorkoutClass(
                (int) $row['ClassId'],
                (int) $row['TrainerId'],
                (int) $row['ClassTypeId'],
                (string) $row['ClassDateTime'],
                (int) $row['Capacity']
            );
        }

        return $classes;
    }

    public static function getAllWorkoutClasses(PDO $db): array {
        $stmt = $db->prepare('
            SELECT *
            FROM Classes
            ORDER BY datetime(ClassDateTime) ASC
        ');

        $stmt->execute();

        $classes = [];

        while ($row = $stmt->fetch()) {
            $classes[] = new WorkoutClass(
                (int) $row['ClassId'],
                (int) $row['TrainerId'],
                (int) $row['ClassTypeId'],
                (string) $row['ClassDateTime'],
                (int) $row['Capacity']
            );
        }

        return $classes;
    }

    public static function getUpcomingWorkoutClasses(PDO $db): array {
        $stmt = $db->prepare('
            SELECT *
            FROM Classes
            WHERE datetime(ClassDateTime) >= datetime("now", "localtime")
            ORDER BY datetime(ClassDateTime) ASC
        ');

        $stmt->execute();

        $classes = [];

        while ($row = $stmt->fetch()) {
            $classes[] = new WorkoutClass(
                (int) $row['ClassId'],
                (int) $row['TrainerId'],
                (int) $row['ClassTypeId'],
                (string) $row['ClassDateTime'],
                (int) $row['Capacity']
            );
        }

        return $classes;
    }

    public static function getPastWorkoutClasses(PDO $db): array {
        $stmt = $db->prepare('
            SELECT *
            FROM Classes
            WHERE datetime(ClassDateTime) < datetime("now", "localtime")
            ORDER BY datetime(ClassDateTime) DESC
        ');

        $stmt->execute();

        $classes = [];

        while ($row = $stmt->fetch()) {
            $classes[] = new WorkoutClass(
                (int) $row['ClassId'],
                (int) $row['TrainerId'],
                (int) $row['ClassTypeId'],
                (string) $row['ClassDateTime'],
                (int) $row['Capacity']
            );
        }

        return $classes;
    }

    public static function getEnrollmentCount(PDO $db, int $classId): int {
        $stmt = $db->prepare('
            SELECT COUNT(*) AS EnrollmentCount
            FROM Enrollments
            WHERE ClassId = ?
            AND Status = "active"
        ');

        $stmt->execute([$classId]);
        $row = $stmt->fetch();

        return (int) $row['EnrollmentCount'];
    }

    public static function isFull(PDO $db, int $classId, int $capacity): bool {
        return WorkoutClass::getEnrollmentCount($db, $classId) >= $capacity;
    }

    public function getTrainerName(PDO $db): string {
        $trainer = Trainers::getTrainer($db, $this->trainerId);

        if ($trainer === null) {
            return 'Unknown trainer';
        }

        return $trainer->getName($db);
    }

    public static function normalizeClassDateTime(string $dateTime): ?string {
        if ($dateTime === '') {
            return null;
        }

        $dateTime = str_replace('T', ' ', $dateTime);
        $timestamp = strtotime($dateTime);

        if ($timestamp === false) {
            return null;
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

    private static function validateClassData(
        PDO $db,
        int $trainerId,
        int $classTypeId,
        string $classDateTime,
        int $capacity
    ): string {
        if ($trainerId <= 0 || $classTypeId <= 0) {
            throw new InvalidArgumentException('Invalid trainer or class type.');
        }

        if ($capacity < 1) {
            throw new InvalidArgumentException('Capacity must be at least 1.');
        }

        $classDateTime = WorkoutClass::normalizeClassDateTime($classDateTime);

        if ($classDateTime === null) {
            throw new InvalidArgumentException('Invalid class date.');
        }

        if (strtotime($classDateTime) < time()) {
            throw new InvalidArgumentException('Class date must be in the future.');
        }

        if (Trainers::getTrainer($db, $trainerId) === null) {
            throw new InvalidArgumentException('Trainer not found.');
        }

        if (WorkoutClassType::getWorkoutClassType($db, $classTypeId) === null) {
            throw new InvalidArgumentException('Class type not found.');
        }

        return $classDateTime;
    }

    public static function addWorkoutClassToDb(
        PDO $db,
        int $trainerId,
        int $classTypeId,
        string $date,
        int $capacity
    ): void {
        $stmt = $db->prepare('
            INSERT INTO Classes (TrainerId, ClassTypeId, ClassDateTime, Capacity)
            VALUES (?, ?, ?, ?)
        ');

        $stmt->execute([$trainerId, $classTypeId, $date, $capacity]);
    }

    public static function createClass(
        PDO $db,
        int $trainerId,
        int $classTypeId,
        string $classDateTime,
        int $capacity
    ): void {
        $classDateTime = WorkoutClass::validateClassData(
            $db,
            $trainerId,
            $classTypeId,
            $classDateTime,
            $capacity
        );

        WorkoutClass::addWorkoutClassToDb(
            $db,
            $trainerId,
            $classTypeId,
            $classDateTime,
            $capacity
        );
    }

    public function updateClass(
        PDO $db,
        int $trainerId,
        int $classTypeId,
        string $classDateTime,
        int $capacity
    ): void {
        $classDateTime = WorkoutClass::validateClassData(
            $db,
            $trainerId,
            $classTypeId,
            $classDateTime,
            $capacity
        );

        $stmt = $db->prepare('
            UPDATE Classes
            SET TrainerId = ?,
                ClassTypeId = ?,
                ClassDateTime = ?,
                Capacity = ?
            WHERE ClassId = ?
        ');

        $stmt->execute([
            $trainerId,
            $classTypeId,
            $classDateTime,
            $capacity,
            $this->id
        ]);
    }

    public static function deleteClass(PDO $db, int $id): void {
        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid class.');
        }

        if (WorkoutClass::getWorkoutClass($db, $id) === null) {
            throw new InvalidArgumentException('Class not found.');
        }

        $stmt = $db->prepare('
            DELETE FROM Classes
            WHERE ClassId = ?
        ');

        $stmt->execute([$id]);
    }
}
?>