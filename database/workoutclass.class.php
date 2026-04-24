<?php
require_once(__DIR__ . '/users.class.php');
require_once(__DIR__ . '/enrollments.class.php');
require_once(__DIR__ . '/workoutclasstype.class.php');
require_once(__DIR__ . '/trainers.class.php');
class WorkoutClass {
    private int $id;
    private int $trainerId;
    private int $classTypeId;
    private string $day;
    private string $startTime;
    private int $capacity;

    public function __construct(
        int $id,
        int $trainerId,
        int $classTypeId,
        string $day,
        string $startTime,
        int $capacity
    ) {
        $this->id = $id;
        $this->trainerId = $trainerId;
        $this->classTypeId = $classTypeId;
        $this->day = $day;
        $this->startTime = $startTime;
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

    public function getDay(): string {
        return $this->day;
    }

    public function getStartTime(): string {
        return $this->startTime;
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
            $row['ClassId'],
            $row['TrainerId'],
            $row['ClassTypeId'],
            $row['Day'],
            $row['StartTime'],
            $row['Capacity']
        );
    }


    public function isFull(PDO $db) : bool {
        $stmt = $db->prepare('
            SELECT COUNT(*) AS EnrollmentCount
            FROM Enrollments
            WHERE ClassId = ?
        ');

        $stmt->execute([$this->id]);
        $row = $stmt->fetch();

        return $row['EnrollmentCount'] >= $this->capacity;
    }

}