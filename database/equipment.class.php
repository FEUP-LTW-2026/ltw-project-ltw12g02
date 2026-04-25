<?php

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

    public static function getEquipment(PDO $db, int $id): ?Equipment {
        $stmt = $db->prepare(
            'SELECT *
             FROM Equipment
             WHERE EquipmentId = ?'
        );

        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Equipment(
            intval($row['EquipmentId']),
            $row['Name'],
            $row['Type'],
            intval($row['Quantity']),
            $row['AvailabilityStatus']
        );
    }

    public static function getAllEquipment(PDO $db): array {
        $stmt = $db->prepare(
            'SELECT *
             FROM Equipment
             ORDER BY Name'
        );

        $stmt->execute();

        $equipment = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $equipment[] = new Equipment(
                intval($row['EquipmentId']),
                $row['Name'],
                $row['Type'],
                intval($row['Quantity']),
                $row['AvailabilityStatus']
            );
        }

        return $equipment;
    }

    public static function getAvailableEquipment(PDO $db): array {
        $stmt = $db->prepare(
            'SELECT *
             FROM Equipment
             WHERE AvailabilityStatus = ?
             ORDER BY Name'
        );

        $stmt->execute(['available']);

        $equipment = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $equipment[] = new Equipment(
                intval($row['EquipmentId']),
                $row['Name'],
                $row['Type'],
                intval($row['Quantity']),
                $row['AvailabilityStatus']
            );
        }

        return $equipment;
    }
}