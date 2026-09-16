<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Notification.php';

/**
 * Payment
 * Records monthly fee payments and updates the payment status shown
 * in the student profile (FR-07). After recording a payment, a
 * simulated parent notification is created (FR-08 / UC-04) since the
 * source documents do not specify a real SMS gateway.
 */
class Payment
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function record(array $data): int
    {
        $sql = "INSERT INTO payments (student_id, amount, month, payment_date, status, notes)
                VALUES (:student_id, :amount, :month, :payment_date, :status, :notes)";
        $id = (int)$this->db->insert($sql, [
            'student_id'   => $data['student_id'],
            'amount'       => $data['amount'],
            'month'        => $data['month'],
            'payment_date' => $data['payment_date'],
            'status'       => $data['status'] ?? 'Paid',
            'notes'        => $data['notes'] ?? null,
        ]);

        if (($data['status'] ?? 'Paid') === 'Paid') {
            $student = $this->db->selectOne("SELECT * FROM students WHERE student_id = :id", ['id' => $data['student_id']]);
            if ($student) {
                $notifier = new Notification();
                $notifier->send(
                    (int)$data['student_id'],
                    "Dear Parent, {$data['month']} tuition fee of Rs." . number_format((float)$data['amount'], 2) .
                    " for {$student['first_name']} {$student['last_name']} has been received. Thank you.",
                    'SMS'
                );
            }
        }

        return $id;
    }

    public function updateStatus(int $paymentId, string $status): bool
    {
        return $this->db->execute(
            "UPDATE payments SET status = :status WHERE payment_id = :id",
            ['status' => $status, 'id' => $paymentId]
        ) >= 0;
    }

    public function forStudent(int $studentId): array
    {
        return $this->db->select(
            "SELECT * FROM payments WHERE student_id = :sid ORDER BY payment_date DESC",
            ['sid' => $studentId]
        );
    }

    public function all(): array
    {
        return $this->db->select(
            "SELECT p.*, s.first_name, s.last_name
             FROM payments p JOIN students s ON s.student_id = p.student_id
             ORDER BY p.created_at DESC"
        );
    }

    public function delete(int $id): bool
    {
        return $this->db->execute("DELETE FROM payments WHERE payment_id = :id", ['id' => $id]) > 0;
    }

    public function totalIncome(): float
    {
        return (float)$this->db->selectOne("SELECT COALESCE(SUM(amount),0) AS t FROM payments WHERE status = 'Paid'")['t'];
    }
}
