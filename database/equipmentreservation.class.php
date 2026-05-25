<?php
declare(strict_types = 1);

require_once(__DIR__ . '/equipment.class.php');

class EquipmentReservation {
    private static function countReservationsForSlot(
        PDO $db,
        int $equipmentId,
        DateTimeImmutable $reservationDateTime,
        int $duration
    ): int {
        $start = $reservationDateTime->format('Y-m-d H:i:s');

        $end = $reservationDateTime
            ->modify('+' . $duration . ' minutes')
            ->format('Y-m-d H:i:s');

        $stmt = $db->prepare('
            SELECT COUNT(*)
            FROM EquipmentReservations
            WHERE EquipmentId = ?
              AND Status = "active"
              AND datetime(ReservationDateTime) < datetime(?)
              AND datetime(ReservationDateTime, "+" || Duration || " minutes") > datetime(?)
        ');

        $stmt->execute([
            $equipmentId,
            $end,
            $start
        ]);

        return (int)$stmt->fetchColumn();
    }

    public static function getAvailableQuantityForSlot(
        PDO $db,
        Equipment $equipment,
        DateTimeImmutable $reservationDateTime,
        int $duration
    ): int {
        $reservedQuantity = self::countReservationsForSlot(
            $db,
            $equipment->getId(),
            $reservationDateTime,
            $duration
        );

        return max(0, $equipment->getQuantity() - $reservedQuantity);
    }

    public static function getCurrentAvailableQuantity(PDO $db, Equipment $equipment): int {
        $now = new DateTimeImmutable();

        $stmt = $db->prepare('
            SELECT COUNT(*)
            FROM EquipmentReservations
            WHERE EquipmentId = ?
              AND Status = "active"
              AND datetime(ReservationDateTime) <= datetime(?)
              AND datetime(ReservationDateTime, "+" || Duration || " minutes") > datetime(?)
        ');

        $nowFormatted = $now->format('Y-m-d H:i:s');

        $stmt->execute([
            $equipment->getId(),
            $nowFormatted,
            $nowFormatted
        ]);

        $reservedNow = (int)$stmt->fetchColumn();

        return max(0, $equipment->getQuantity() - $reservedNow);
    }

    public static function createReservation( PDO $db, int $userId, int $equipmentId, DateTimeImmutable $reservationDateTime, int $duration): void {
        $allowedDurations = [30, 60, 90, 120];

        if (!in_array($duration, $allowedDurations, true)) {
            throw new Exception('Invalid reservation duration.');
        }

        $now = new DateTimeImmutable();

        if ($reservationDateTime <= $now) {
            throw new Exception('The reservation must be in the future.');
        }

        $equipment = Equipment::getEquipment($db, $equipmentId);

        if ($equipment === null) {
            throw new Exception('Equipment not found.');
        }

        if (strtolower($equipment->getStatus()) !== 'available') {
            throw new Exception('This equipment is not available for reservation.');
        }

        if ($equipment->getQuantity() <= 0) {
            throw new Exception('There are no units available.');
        }

        $reservationStart = $reservationDateTime->format('Y-m-d H:i:s');
        $reservationEnd = $reservationDateTime
            ->modify('+' . $duration . ' minutes')
            ->format('Y-m-d H:i:s');

        $stmt = $db->prepare('
            SELECT COUNT(*)
            FROM EquipmentReservations
            WHERE UserId = ?
            AND Status = "active"
            AND datetime(ReservationDateTime) < datetime(?)
            AND datetime(ReservationDateTime, "+" || Duration || " minutes") > datetime(?)
        ');

        $stmt->execute([
            $userId,
            $reservationEnd,
            $reservationStart
        ]);

        if ((int)$stmt->fetchColumn() > 0) {
            throw new Exception('You already have another equipment reservation during that time.');
        }

        $db->beginTransaction();

        try {
            $stmt = $db->prepare('
                INSERT INTO EquipmentReservations (
                    UserId,
                    EquipmentId,
                    ReservationDateTime,
                    Duration,
                    Status
                )
                VALUES (?, ?, ?, ?, "active")
            ');

            $stmt->execute([
                $userId,
                $equipmentId,
                $reservationStart,
                $duration
            ]);

            Equipment::decreaseQuantity($db, $equipmentId);

            $db->commit();
        } catch (Exception $exception) {
            $db->rollBack();
            throw $exception;
        }
    }

    public static function getUserUpcomingReservations(PDO $db, int $userId): array {
        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');

        $stmt = $db->prepare('
            SELECT
                EquipmentReservations.EquipmentReservationId,
                EquipmentReservations.UserId,
                EquipmentReservations.EquipmentId,
                EquipmentReservations.ReservationDateTime,
                EquipmentReservations.Duration,
                EquipmentReservations.Status,
                datetime(
                    EquipmentReservations.ReservationDateTime,
                    "+" || EquipmentReservations.Duration || " minutes"
                ) AS EndDateTime,
                Equipment.Name,
                Equipment.Type
            FROM EquipmentReservations
            JOIN Equipment USING (EquipmentId)
            WHERE EquipmentReservations.UserId = ?
              AND EquipmentReservations.Status = "active"
              AND datetime(
                    EquipmentReservations.ReservationDateTime,
                    "+" || EquipmentReservations.Duration || " minutes"
                  ) >= datetime(?)
            ORDER BY EquipmentReservations.ReservationDateTime ASC
        ');

        $stmt->execute([$userId, $now]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function cancelReservation(PDO $db, int $reservationId, int $userId): void {
        $stmt = $db->prepare('
            SELECT EquipmentId
            FROM EquipmentReservations
            WHERE EquipmentReservationId = ?
            AND UserId = ?
            AND Status = "active"
        ');

        $stmt->execute([$reservationId, $userId]);

        $equipmentId = $stmt->fetchColumn();

        if ($equipmentId === false) {
            throw new Exception('Reservation not found.');
        }

        $db->beginTransaction();

        try {
            $stmt = $db->prepare('
                UPDATE EquipmentReservations
                SET Status = "cancelled"
                WHERE EquipmentReservationId = ?
                AND UserId = ?
                AND Status = "active"
            ');

            $stmt->execute([$reservationId, $userId]);

            Equipment::increaseQuantity($db, (int)$equipmentId);

            $db->commit();
        } catch (Exception $exception) {
            $db->rollBack();
            throw $exception;
        }
    }
}
?>