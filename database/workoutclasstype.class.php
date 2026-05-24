<?php

require_once(__DIR__ . '/workoutclass.class.php');

class WorkoutClassType {

    private int $id;
    private string $name;
    private string $description;
    private int $duration;

    public function __construct(
        int $id,
        string $name,
        string $description,
        int $duration
    ) { 
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->duration = $duration;
    }

    public function getId(): int { 
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getDuration(): int {
        return $this->duration;
    }

    public static function getWorkoutClassType(PDO $db, int $id): ?WorkoutClassType {
        $stmt = $db->prepare('
            SELECT * 
            FROM ClassType
            WHERE ClassTypeId = ?
        ');

        $stmt->execute([$id]);

        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return new WorkoutClassType(
            (int)$row['ClassTypeId'],
            $row['Name'],
            $row['Description'],
            (int)$row['Duration']
        );
    }

    public function getWorkoutClasses(PDO $db): array {
        $stmt = $db->prepare('
            SELECT *
            FROM Classes
            WHERE ClassTypeId = ?  
            ORDER BY ClassDateTime
        ');

        $stmt->execute([$this->id]);

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

    public function getImagePath(): string {
        return '../assets/class' . $this->id . '.png';
    }

    public static function getAllWorkoutClassTypes(PDO $db): array {
    $stmt = $db->prepare('
        SELECT *
        FROM ClassType
        ORDER BY ClassTypeId
    ');

    $stmt->execute();

    $types = [];

    while ($row = $stmt->fetch()) {
        $types[] = new WorkoutClassType(
            (int)$row['ClassTypeId'],
            $row['Name'],
            $row['Description'],
            (int)$row['Duration']
        );
    }

    return $types;
}
}