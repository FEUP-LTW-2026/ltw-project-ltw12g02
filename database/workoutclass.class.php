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
            (int)$row['ClassId'],
            (int)$row['TrainerId'],
            (int)$row['ClassTypeId'],
            $row['ClassDateTime'],
            (int)$row['Capacity']
        );
    }

    public function isFull(PDO $db): bool {
        $stmt = $db->prepare('
            SELECT COUNT(*) AS EnrollmentCount
            FROM Enrollments
            WHERE ClassId = ?
            AND Status = "active"
        ');

        $stmt->execute([$this->id]);
        $row = $stmt->fetch();

        return (int)$row['EnrollmentCount'] >= $this->capacity;
    }

    public function getTrainerName(PDO $db) : string{

        return Trainers::getTrainer($db,$this->trainerId)->getName($db);


    }

    public static function addWorkoutClassToDb(PDO $db, int $trainerId,int $classTypeId,string $date, int $capacity) : void{

        $stmt = $db->prepare('
        INSERT INTO Classes (TrainerId,ClassTypeId,ClassDateTime,Capacity)
        VALUES (?,?,?,?)
        ');

        $stmt->execute([$trainerId,$classTypeId,$date,$capacity]);

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

    public static function createClass(PDO $db, int $trainer_id, int $class_type_id, string $class_datetime, int $capacity): void {

        WorkoutClass::addWorkoutClassToDb(
            $db,
            $trainer_id,
            $class_type_id,
            $class_datetime,
            $capacity
        );

    }

    public function updateClass(PDO $db, int $trainer_id, int $class_type_id, string $class_datetime, int $capacity): void {
        
        $stmt = $db->prepare('
            UPDATE Classes
            SET TrainerId = ?,
                ClassTypeId = ?,
                ClassDateTime = ?,
                Capacity = ?
            WHERE ClassId = ?
        ');

        $stmt->execute([$trainer_id, $class_type_id, $class_datetime, $capacity, $this->id]);
    }

    function normalizeClassDateTime(string $dateTime): ?string {
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

    public static function deleteClass(PDO $db, int $id): void {
        $stmt = $db->prepare('
            DELETE FROM Classes
            WHERE ClassId = ?
        ');

        $stmt->execute([$id]);
    }

}