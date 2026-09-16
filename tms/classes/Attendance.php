<?php
require_once __DIR__ . '/Database.php';

/**
 * Attendance
 * Records and retrieves student attendance (FR-06 / UC-03).
 * The original SRS describes QR-code based marking; here the QR code
 * value tied to a student's profile is used to identify the student.
 */
class Attendance
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Mark attendance by scanning / entering a student's QR code value */
    public function markByQrCode(string $qrCode, string $subject, string $status = 'Present'): array
    {
        $student = $this->db->selectOne("SELECT * FROM students WHERE qr_code = :qr", ['qr' => $qrCode]);
        if (!$student) {
            return ['success' => false, 'message' => 'No student found for this QR code.'];
        }

        $existing = $this->db->selectOne(
            "SELECT * FROM attendance WHERE student_id = :sid AND date = CURDATE() AND subject = :subj",
            ['sid' => $student['student_id'], 'subj' => $subject]
        );
        if ($existing) {
            return ['success' => false, 'message' => 'Attendance already marked for this student today.'];
        }

        $this->db->insert(
            "INSERT INTO attendance (student_id, date, status, subject) VALUES (:sid, CURDATE(), :status, :subject)",
            ['sid' => $student['student_id'], 'status' => $status, 'subject' => $subject]
        );

        return ['success' => true, 'message' => "Attendance marked for {$student['first_name']} {$student['last_name']}."];
    }

    public function markManual(int $studentId, string $date, string $subject, string $status): int
    {
        return (int)$this->db->insert(
            "INSERT INTO attendance (student_id, date, status, subject) VALUES (:sid, :date, :status, :subject)",
            ['sid' => $studentId, 'date' => $date, 'status' => $status, 'subject' => $subject]
        );
    }

    public function forStudent(int $studentId): array
    {
        return $this->db->select(
            "SELECT * FROM attendance WHERE student_id = :sid ORDER BY date DESC",
            ['sid' => $studentId]
        );
    }

    public function all(): array
    {
        return $this->db->select(
            "SELECT a.*, s.first_name, s.last_name
             FROM attendance a JOIN students s ON s.student_id = a.student_id
             ORDER BY a.date DESC, a.created_at DESC"
        );
    }

    public function delete(int $id): bool
    {
        return $this->db->execute("DELETE FROM attendance WHERE attendance_id = :id", ['id' => $id]) > 0;
    }
}
