<?php
require_once __DIR__ . '/Database.php';

/**
 * Notification
 * The original documents describe notifying parents by SMS after a
 * fee payment is recorded. A real SMS gateway is not specified in the
 * source documents, so this class logs a notification record that the
 * Admin/Student UI can display, standing in for that channel.
 */
class Notification
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function send(int $studentId, string $message, string $channel = 'SMS'): int
    {
        return (int)$this->db->insert(
            "INSERT INTO notifications (student_id, message, channel) VALUES (:sid, :msg, :ch)",
            ['sid' => $studentId, 'msg' => $message, 'ch' => $channel]
        );
    }

    public function forStudent(int $studentId): array
    {
        return $this->db->select(
            "SELECT * FROM notifications WHERE student_id = :sid ORDER BY sent_at DESC",
            ['sid' => $studentId]
        );
    }

    public function all(int $limit = 20): array
    {
        $limit = (int)$limit;
        return $this->db->select(
            "SELECT n.*, s.first_name, s.last_name
             FROM notifications n JOIN students s ON s.student_id = n.student_id
             ORDER BY n.sent_at DESC LIMIT {$limit}"
        );
    }
}
