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
}
?>