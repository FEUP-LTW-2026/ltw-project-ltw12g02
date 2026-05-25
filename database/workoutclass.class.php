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

    public static function getFilteredClasses(
        PDO $db,
        String $classTypeId,
        ?string $trainerId,
        ?string $date,
        ?string $time
    ): array {

        $sql = '
            SELECT *
            FROM Classes
            WHERE ClassDateTime >= datetime() AND ClassTypeId = ?
        ';

        $params = [];
        $params[] = $classTypeId;

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
                $row['ClassId'],
                $row['TrainerId'],
                $row['ClassTypeId'],
                $row['ClassDateTime'],
                $row['Capacity']
            );
        }

        return $classes;
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
}