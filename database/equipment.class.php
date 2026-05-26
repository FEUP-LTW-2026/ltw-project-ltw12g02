<?php
declare(strict_types = 1);

class Equipment {
    private int $id;
    private string $name;
    private string $type;
    private int $quantity;
    private string $status;

    public function __construct(
        int $id,
        string $name,
        string $type,
        int $quantity,
        string $status
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->quantity = $quantity;
        $this->status = $status;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getType(): string {
        return $this->type;
    }

    public function getQuantity(): int {
        return $this->quantity;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public static function getAllEquipment(PDO $db): array {
        $stmt = $db->prepare('
            SELECT *
            FROM Equipment
            ORDER BY Type, Name
        ');

        $stmt->execute();

        $equipment = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $equipment[] = new Equipment(
                (int)$row['EquipmentId'],
                $row['Name'],
                $row['Type'],
                (int)$row['Quantity'],
                $row['AvailabilityStatus']
            );
        }

        return $equipment;
    }

    public static function getEquipment(PDO $db, int $id): ?Equipment {
        $stmt = $db->prepare('
            SELECT *
            FROM Equipment
            WHERE EquipmentId = ?
        ');

        $stmt->execute([$id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return new Equipment(
            (int)$row['EquipmentId'],
            $row['Name'],
            $row['Type'],
            (int)$row['Quantity'],
            $row['AvailabilityStatus']
        );
    }

    public static function addEquipment( PDO $db, string $name, string $type, int $quantity, string $status): int {
        self::validateEquipmentData($name, $type, $quantity, $status);

        $stmt = $db->prepare('
            INSERT INTO Equipment (
                Name,
                Type,
                Quantity,
                AvailabilityStatus
            )
            VALUES (?, ?, ?, ?)
        ');

        $stmt->execute([
            trim($name),
            trim($type),
            $quantity,
            $status
        ]);

        return (int)$db->lastInsertId();
    }

    public static function updateEquipmentStatus(PDO $db,int $equipmentId,string $status): void {
        self::validateStatus($status);

        $equipment = self::getEquipment($db, $equipmentId);

        if ($equipment === null) {
            throw new Exception('Equipment not found.');
        }

        $stmt = $db->prepare('
            UPDATE Equipment
            SET AvailabilityStatus = ?
            WHERE EquipmentId = ?
        ');

        $stmt->execute([
            $status,
            $equipmentId
        ]);
    }

    public static function updateEquipment( PDO $db, int $equipmentId, string $name, string $type, int $quantity, string $status): void {
        self::validateEquipmentData($name, $type, $quantity, $status);

        $equipment = self::getEquipment($db, $equipmentId);

        if ($equipment === null) {
            throw new Exception('Equipment not found.');
        }

        $stmt = $db->prepare('
            UPDATE Equipment
            SET Name = ?,
                Type = ?,
                Quantity = ?,
                AvailabilityStatus = ?
            WHERE EquipmentId = ?
        ');

        $stmt->execute([
            trim($name),
            trim($type),
            $quantity,
            $status,
            $equipmentId
        ]);
    }

    public static function deleteEquipment(PDO $db, int $equipmentId): void {
        if (self::hasActiveReservations($db, $equipmentId)) {
            throw new Exception('This equipment has active reservations and cannot be removed.');
        }

        $stmt = $db->prepare('
            DELETE FROM Equipment
            WHERE EquipmentId = ?
        ');

        $stmt->execute([$equipmentId]);

        if ($stmt->rowCount() === 0) {
            throw new Exception('Equipment not found.');
        }
    }

    private static function hasActiveReservations(PDO $db, int $equipmentId): bool {
        $stmt = $db->prepare('
            SELECT COUNT(*)
            FROM EquipmentReservations
            WHERE EquipmentId = ?
              AND Status = "active"
        ');

        $stmt->execute([$equipmentId]);

        return (int)$stmt->fetchColumn() > 0;
    }

    public static function decreaseQuantity(PDO $db, int $equipmentId): void {
        $stmt = $db->prepare('
            UPDATE Equipment
            SET Quantity = Quantity - 1
            WHERE EquipmentId = ?
              AND Quantity > 0
        ');

        $stmt->execute([$equipmentId]);
    }

    public static function increaseQuantity(PDO $db, int $equipmentId): void {
        $stmt = $db->prepare('
            UPDATE Equipment
            SET Quantity = Quantity + 1
            WHERE EquipmentId = ?
        ');

        $stmt->execute([$equipmentId]);
    }

    private static function validateEquipmentData( string $name, string $type, int $quantity, string $status): void {
        if (trim($name) === '') {
            throw new Exception('Equipment name is required.');
        }

        if (trim($type) === '') {
            throw new Exception('Equipment type is required.');
        }

        if ($quantity < 0) {
            throw new Exception('Quantity cannot be negative.');
        }

        self::validateStatus($status);
    }

    private static function validateStatus(string $status): void {
        $allowedStatuses = [
            'available',
            'maintenance',
            'unavailable'
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            throw new Exception('Invalid equipment status.');
        }
    }
}
?>