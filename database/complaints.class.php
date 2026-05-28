<?php
declare(strict_types = 1);

class Complaints {

    private int $complaint_id;
    private int $user_id;
    private string $reason;
    private string $details;
    private string $complaint_date;
    private string $response;

    public function __construct(
        int $complaint_id,
        int $user_id,
        string $reason,
        string $details,
        string $complaint_date,
        ?string $response = ''
    ) {
        $this->complaint_id = $complaint_id;
        $this->user_id = $user_id;
        $this->reason = $reason;
        $this->details = $details;
        $this->complaint_date = $complaint_date;
        $this->response = $response ?? '';
    }

    public function get_complaint_id(): int {
        return $this->complaint_id;
    }

    public function get_user_id(): int {
        return $this->user_id;
    }

    public function get_reason(): string {
        return $this->reason;
    }

    public function get_details(): string {
        return $this->details;
    }

    public function get_complaint_date(): string {
        return $this->complaint_date;
    }

    public function get_response(): string {
        return $this->response;
    }

    public function isAnswered(): bool {
        return trim($this->response) !== '';
    }

    public static function getComplaint(PDO $db, int $id): ?Complaints {
        $stmt = $db->prepare('
            SELECT *
            FROM Complaints
            WHERE ComplaintId = ?
        ');

        $stmt->execute([$id]);

        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return new Complaints(
            (int)$row['ComplaintId'],
            (int)$row['UserId'],
            (string)$row['Reason'],
            (string)$row['Details'],
            (string)$row['ComplaintDate'],
            $row['Response'] ?? ''
        );
    }

    public static function getAllComplaints(PDO $db): array {
        $stmt = $db->prepare('
            SELECT *
            FROM Complaints
            ORDER BY datetime(ComplaintDate) DESC
        ');

        $stmt->execute();

        $complaints = [];

        while ($row = $stmt->fetch()) {
            $complaints[] = new Complaints(
                (int)$row['ComplaintId'],
                (int)$row['UserId'],
                (string)$row['Reason'],
                (string)$row['Details'],
                (string)$row['ComplaintDate'],
                $row['Response'] ?? ''
            );
        }

        return $complaints;
    }

    public static function getUserComplaints(PDO $db, int $userId): array {
        $stmt = $db->prepare('
            SELECT *
            FROM Complaints
            WHERE UserId = ?
            ORDER BY datetime(ComplaintDate) DESC
        ');

        $stmt->execute([$userId]);

        $complaints = [];

        while ($row = $stmt->fetch()) {
            $complaints[] = new Complaints(
                (int)$row['ComplaintId'],
                (int)$row['UserId'],
                (string)$row['Reason'],
                (string)$row['Details'],
                (string)$row['ComplaintDate'],
                $row['Response'] ?? ''
            );
        }

        return $complaints;
    }

    public static function addComplaintToDb(PDO $db, int $userId, string $reason, string $details): void {
        $stmt = $db->prepare('
            INSERT INTO Complaints (
                UserId,
                Reason,
                Details,
                ComplaintDate,
                Response
            )
            VALUES (?, ?, ?, datetime("now", "localtime"), "")
        ');

        $stmt->execute([
            $userId,
            $reason,
            $details
        ]);
    }

    public static function createComplaint(PDO $db, int $userId, string $reason, string $details): ?Complaints {
        Complaints::addComplaintToDb($db, $userId, $reason, $details);

        $id = (int)$db->lastInsertId();

        return Complaints::getComplaint($db, $id);
    }

    public static function updateResponse(PDO $db, int $id, string $response): void {
        $stmt = $db->prepare('
            UPDATE Complaints
            SET Response = ?
            WHERE ComplaintId = ?
        ');

        $stmt->execute([
            $response,
            $id
        ]);
    }
}
?>