<?php
require_once __DIR__ . '/Database.php';

/**
 * Timetable
 * Admin adds/updates class schedules; students search & view them (FR-09/FR-10).
 */
class Timetable
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function add(array $data): int
    {
        $sql = "INSERT INTO timetable (tutor_id, subject, class_date, start_time, end_time, classroom)
                VALUES (:tutor_id, :subject, :class_date, :start_time, :end_time, :classroom)";
        return (int)$this->db->insert($sql, [
            'tutor_id'   => $data['tutor_id'],
            'subject'    => $data['subject'],
            'class_date' => $data['class_date'],
            'start_time' => $data['start_time'],
            'end_time'   => $data['end_time'],
            'classroom'  => $data['classroom'] ?? null,
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE timetable SET tutor_id=:tutor_id, subject=:subject, class_date=:class_date,
                start_time=:start_time, end_time=:end_time, classroom=:classroom WHERE timetable_id=:id";
        return $this->db->execute($sql, [
            'tutor_id'   => $data['tutor_id'],
            'subject'    => $data['subject'],
            'class_date' => $data['class_date'],
            'start_time' => $data['start_time'],
            'end_time'   => $data['end_time'],
            'classroom'  => $data['classroom'] ?? null,
            'id'         => $id,
        ]) >= 0;
    }

    public function delete(int $id): bool
    {
        return $this->db->execute("DELETE FROM timetable WHERE timetable_id = :id", ['id' => $id]) > 0;
    }

    public function all(string $search = ''): array
    {
        if ($search !== '') {
            $like = "%{$search}%";
            return $this->db->select(
                "SELECT t.*, tu.name AS tutor_name FROM timetable t
                 JOIN tutors tu ON tu.tutor_id = t.tutor_id
                 WHERE t.subject LIKE :s OR tu.name LIKE :s
                 ORDER BY t.class_date ASC, t.start_time ASC",
                ['s' => $like]
            );
        }
        return $this->db->select(
            "SELECT t.*, tu.name AS tutor_name FROM timetable t
             JOIN tutors tu ON tu.tutor_id = t.tutor_id
             ORDER BY t.class_date ASC, t.start_time ASC"
        );
    }

    public function forTutor(int $tutorId): array
    {
        return $this->db->select(
            "SELECT * FROM timetable WHERE tutor_id = :id ORDER BY class_date ASC, start_time ASC",
            ['id' => $tutorId]
        );
    }

    public function findById(int $id): ?array
    {
        return $this->db->selectOne("SELECT * FROM timetable WHERE timetable_id = :id", ['id' => $id]);
    }
}
