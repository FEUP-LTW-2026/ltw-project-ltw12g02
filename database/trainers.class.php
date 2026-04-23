<?php

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
}