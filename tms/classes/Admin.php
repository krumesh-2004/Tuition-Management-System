<?php
require_once __DIR__ . '/User.php';

/**
 * Admin - manages students, tutors, timetable, attendance and payments.
 */
class Admin extends User
{
    public function getTableName(): string { return 'admins'; }
    public function getIdColumn(): string { return 'admin_id'; }
    public function getRole(): string { return 'admin'; }

    public function findById(int $id): ?array
    {
        return $this->db->selectOne("SELECT * FROM admins WHERE admin_id = :id", ['id' => $id]);
    }

    /** Dashboard statistics */
    public function getStats(): array
    {
        $studentCount = $this->db->selectOne("SELECT COUNT(*) AS c FROM students")['c'];
        $tutorCount   = $this->db->selectOne("SELECT COUNT(*) AS c FROM tutors")['c'];
        $classesToday = $this->db->selectOne("SELECT COUNT(*) AS c FROM timetable WHERE class_date = CURDATE()")['c'];
        $pendingPay   = $this->db->selectOne("SELECT COUNT(*) AS c FROM payments WHERE status != 'Paid'")['c'];
        $totalIncome  = $this->db->selectOne("SELECT COALESCE(SUM(amount),0) AS t FROM payments WHERE status = 'Paid'")['t'];
        $presentToday = $this->db->selectOne("SELECT COUNT(*) AS c FROM attendance WHERE date = CURDATE() AND status = 'Present'")['c'];

        return [
            'students'      => (int)$studentCount,
            'tutors'        => (int)$tutorCount,
            'classesToday'  => (int)$classesToday,
            'pendingPay'    => (int)$pendingPay,
            'totalIncome'   => (float)$totalIncome,
            'presentToday'  => (int)$presentToday,
        ];
    }

    public function recentActivity(int $limit = 6): array
    {
        $limit = (int)$limit; // sanitised before concatenation - safe from SQL injection
        return $this->db->select(
            "SELECT s.first_name, s.last_name, p.amount, p.month, p.payment_date
             FROM payments p JOIN students s ON s.student_id = p.student_id
             WHERE p.status = 'Paid'
             ORDER BY p.created_at DESC LIMIT {$limit}"
        );
    }
}
