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

        while ($row = $stmt->fetch()) {
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
}
?>