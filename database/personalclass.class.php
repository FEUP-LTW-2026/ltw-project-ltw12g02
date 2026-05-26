<?php

require_once(__DIR__ . '/users.class.php');
require_once(__DIR__ . '/trainers.class.php');

class PersonalClass {
    private int $id;
    private int $userId;
    private int $trainerId;
    private string $startDateTime;
    private int $durationMinutes;
    private string $status;
    private ?string $requestMessage;
    private ?string $trainerResponse;
    private string $createdAt;

    public function __construct(
        int $id,
        int $userId,
        int $trainerId,
        string $startDateTime,
        int $durationMinutes,
        string $status,
        ?string $requestMessage,
        ?string $trainerResponse,
        string $createdAt
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->trainerId = $trainerId;
        $this->startDateTime = $startDateTime;
        $this->durationMinutes = $durationMinutes;
        $this->status = $status;
        $this->requestMessage = $requestMessage;
        $this->trainerResponse = $trainerResponse;
        $this->createdAt = $createdAt;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getUserId(): int {
        return $this->userId;
    }

    public function getTrainerId(): int {
        return $this->trainerId;
    }

    public function getStartDateTime(): string {
        return $this->startDateTime;
    }

    public function getDurationMinutes(): int {
        return $this->durationMinutes;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function getRequestMessage(): ?string {
        return $this->requestMessage;
    }

    public function getTrainerResponse(): ?string {
        return $this->trainerResponse;
    }

    public function getCreatedAt(): string {
        return $this->createdAt;
    }

    public static function getPersonalClass(PDO $db, int $id): ?PersonalClass {
        $stmt = $db->prepare('
            SELECT *
            FROM PersonalClasses
            WHERE PersonalClassId = ?
        ');

        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return new PersonalClass(
            (int) $row['PersonalClassId'],
            (int) $row['UserId'],
            (int) $row['TrainerId'],
            (string) $row['StartDateTime'],
            (int) $row['DurationMinutes'],
            (string) $row['Status'],
            $row['RequestMessage'],
            $row['TrainerResponse'],
            (string) $row['CreatedAt']
        );
    }

    public static function getAllPersonalClasses(PDO $db): array {
        $stmt = $db->prepare('
            SELECT *
            FROM PersonalClasses
            ORDER BY datetime(StartDateTime) ASC
        ');

        $stmt->execute();

        $classes = [];

        while ($row = $stmt->fetch()) {
            $classes[] = new PersonalClass(
                (int) $row['PersonalClassId'],
                (int) $row['UserId'],
                (int) $row['TrainerId'],
                (string) $row['StartDateTime'],
                (int) $row['DurationMinutes'],
                (string) $row['Status'],
                $row['RequestMessage'],
                $row['TrainerResponse'],
                (string) $row['CreatedAt']
            );
        }

        return $classes;
    }

    public static function getUpcomingPersonalClasses(PDO $db): array {
        $stmt = $db->prepare('
            SELECT *
            FROM PersonalClasses
            WHERE datetime(StartDateTime) >= datetime("now", "localtime")
            ORDER BY datetime(StartDateTime) ASC
        ');

        $stmt->execute();

        $classes = [];

        while ($row = $stmt->fetch()) {
            $classes[] = new PersonalClass(
                (int) $row['PersonalClassId'],
                (int) $row['UserId'],
                (int) $row['TrainerId'],
                (string) $row['StartDateTime'],
                (int) $row['DurationMinutes'],
                (string) $row['Status'],
                $row['RequestMessage'],
                $row['TrainerResponse'],
                (string) $row['CreatedAt']
            );
        }

        return $classes;
    }

    public static function getPastPersonalClasses(PDO $db): array {
        $stmt = $db->prepare('
            SELECT *
            FROM PersonalClasses
            WHERE datetime(StartDateTime) < datetime("now", "localtime")
            ORDER BY datetime(StartDateTime) DESC
        ');

        $stmt->execute();

        $classes = [];

        while ($row = $stmt->fetch()) {
            $classes[] = new PersonalClass(
                (int) $row['PersonalClassId'],
                (int) $row['UserId'],
                (int) $row['TrainerId'],
                (string) $row['StartDateTime'],
                (int) $row['DurationMinutes'],
                (string) $row['Status'],
                $row['RequestMessage'],
                $row['TrainerResponse'],
                (string) $row['CreatedAt']
            );
        }

        return $classes;
    }

    public static function getUserPersonalClasses(PDO $db, int $userId): array {
        $stmt = $db->prepare('
            SELECT *
            FROM PersonalClasses
            WHERE UserId = ?
            ORDER BY datetime(StartDateTime) ASC
        ');

        $stmt->execute([$userId]);

        $classes = [];

        while ($row = $stmt->fetch()) {
            $classes[] = new PersonalClass(
                (int) $row['PersonalClassId'],
                (int) $row['UserId'],
                (int) $row['TrainerId'],
                (string) $row['StartDateTime'],
                (int) $row['DurationMinutes'],
                (string) $row['Status'],
                $row['RequestMessage'],
                $row['TrainerResponse'],
                (string) $row['CreatedAt']
            );
        }

        return $classes;
    }

    public static function getTrainerPersonalClasses(PDO $db, int $trainerId): array {
        $stmt = $db->prepare('
            SELECT *
            FROM PersonalClasses
            WHERE TrainerId = ?
            ORDER BY datetime(StartDateTime) ASC
        ');

        $stmt->execute([$trainerId]);

        $classes = [];

        while ($row = $stmt->fetch()) {
            $classes[] = new PersonalClass(
                (int) $row['PersonalClassId'],
                (int) $row['UserId'],
                (int) $row['TrainerId'],
                (string) $row['StartDateTime'],
                (int) $row['DurationMinutes'],
                (string) $row['Status'],
                $row['RequestMessage'],
                $row['TrainerResponse'],
                (string) $row['CreatedAt']
            );
        }

        return $classes;
    }

    public static function getTrainerPendingPersonalClasses(PDO $db, int $trainerId): array {
        $stmt = $db->prepare('
            SELECT *
            FROM PersonalClasses
            WHERE TrainerId = ?
            AND Status = "pending"
            ORDER BY datetime(StartDateTime) ASC
        ');

        $stmt->execute([$trainerId]);

        $classes = [];

        while ($row = $stmt->fetch()) {
            $classes[] = new PersonalClass(
                (int) $row['PersonalClassId'],
                (int) $row['UserId'],
                (int) $row['TrainerId'],
                (string) $row['StartDateTime'],
                (int) $row['DurationMinutes'],
                (string) $row['Status'],
                $row['RequestMessage'],
                $row['TrainerResponse'],
                (string) $row['CreatedAt']
            );
        }

        return $classes;
    }

    public static function normalizePersonalClassDateTime(string $dateTime): ?string {
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

    private static function validatePersonalClassData(
        PDO $db,
        int $userId,
        int $trainerId,
        string $startDateTime,
        int $durationMinutes
    ): string {
        if ($userId <= 0 || $trainerId <= 0) {
            throw new InvalidArgumentException('Invalid user or trainer.');
        }

        if ($durationMinutes < 1) {
            throw new InvalidArgumentException('Duration must be at least 1 minute.');
        }

        $startDateTime = PersonalClass::normalizePersonalClassDateTime($startDateTime);

        if ($startDateTime === null) {
            throw new InvalidArgumentException('Invalid class date.');
        }

        if (strtotime($startDateTime) < time()) {
            throw new InvalidArgumentException('Class date must be in the future.');
        }

        if (Users::getUser($db, $userId) === null) {
            throw new InvalidArgumentException('User not found.');
        }

        if (Trainers::getTrainer($db, $trainerId) === null) {
            throw new InvalidArgumentException('Trainer not found.');
        }

        return $startDateTime;
    }

    public static function addPersonalClassToDb(
        PDO $db,
        int $userId,
        int $trainerId,
        string $startDateTime,
        int $durationMinutes,
        ?string $requestMessage
    ): void {
        $stmt = $db->prepare('
            INSERT INTO PersonalClasses (
                UserId,
                TrainerId,
                StartDateTime,
                DurationMinutes,
                Status,
                RequestMessage
            )
            VALUES (?, ?, ?, ?, ?, ?)
        ');

        $stmt->execute([
            $userId,
            $trainerId,
            $startDateTime,
            $durationMinutes,
            'pending',
            $requestMessage
        ]);
    }

    public static function createPersonalClass(
        PDO $db,
        int $userId,
        int $trainerId,
        string $startDateTime,
        int $durationMinutes,
        ?string $requestMessage
    ): void {
        $startDateTime = PersonalClass::validatePersonalClassData(
            $db,
            $userId,
            $trainerId,
            $startDateTime,
            $durationMinutes
        );

        PersonalClass::addPersonalClassToDb(
            $db,
            $userId,
            $trainerId,
            $startDateTime,
            $durationMinutes,
            $requestMessage
        );
    }

    public function acceptPersonalClass(PDO $db, ?string $trainerResponse): void {
        $stmt = $db->prepare('
            UPDATE PersonalClasses
            SET Status = ?,
                TrainerResponse = ?
            WHERE PersonalClassId = ?
        ');

        $stmt->execute([
            'accepted',
            $trainerResponse,
            $this->id
        ]);
    }

    public function rejectPersonalClass(PDO $db, ?string $trainerResponse): void {
        $stmt = $db->prepare('
            UPDATE PersonalClasses
            SET Status = ?,
                TrainerResponse = ?
            WHERE PersonalClassId = ?
        ');

        $stmt->execute([
            'rejected',
            $trainerResponse,
            $this->id
        ]);
    }

    public static function deletePersonalClass(PDO $db, int $id): void {
        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid personal class.');
        }

        if (PersonalClass::getPersonalClass($db, $id) === null) {
            throw new InvalidArgumentException('Personal class not found.');
        }

        $stmt = $db->prepare('
            DELETE FROM PersonalClasses
            WHERE PersonalClassId = ?
        ');

        $stmt->execute([$id]);
    }
}
?>