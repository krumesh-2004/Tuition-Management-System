<?php
require_once __DIR__ . '/User.php';

/**
 * Tutor - manages own profile and submits daily class/income reports (FR-14).
 */
class Tutor extends User
{
    public function getTableName(): string { return 'tutors'; }
    public function getIdColumn(): string { return 'tutor_id'; }
    public function getRole(): string { return 'tutor'; }

    public function findById(int $id): ?array
    {
        return $this->db->selectOne("SELECT * FROM tutors WHERE tutor_id = :id", ['id' => $id]);
    }

    public function all(string $search = ''): array
    {
        if ($search !== '') {
            $like = "%{$search}%";
            return $this->db->select(
                "SELECT * FROM tutors WHERE name LIKE :s OR email LIKE :s OR subject LIKE :s ORDER BY created_at DESC",
                ['s' => $like]
            );
        }
        return $this->db->select("SELECT * FROM tutors ORDER BY created_at DESC");
    }

    /** Admin: add tutor (FR-04) */
    public function add(array $data): int
    {
        $sql = "INSERT INTO tutors (name, email, password, subject, gender, phone, photo_path, status)
                VALUES (:name, :email, :password, :subject, :gender, :phone, :photo_path, 'Active')";
        return (int)$this->db->insert($sql, [
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => self::hashPassword($data['password']),
            'subject'    => $data['subject'],
            'gender'     => $data['gender'] ?? 'Other',
            'phone'      => $data['phone'] ?? null,
            'photo_path' => $data['photo_path'] ?? null,
        ]);
    }

    /** Admin/Tutor: update tutor details (FR-04 / UC-06) */
    public function update(int $id, array $data): bool
    {
        $fields = ['name', 'email', 'subject', 'gender', 'phone', 'status'];
        $setParts = [];
        $params = ['id' => $id];
        foreach ($fields as $f) {
            if (array_key_exists($f, $data)) {
                $setParts[] = "{$f} = :{$f}";
                $params[$f] = $data[$f];
            }
        }
        if (!empty($data['photo_path'])) {
            $setParts[] = "photo_path = :photo_path";
            $params['photo_path'] = $data['photo_path'];
        }
        if (!empty($data['password'])) {
            $setParts[] = "password = :password";
            $params['password'] = self::hashPassword($data['password']);
        }
        if (empty($setParts)) return false;

        $sql = "UPDATE tutors SET " . implode(', ', $setParts) . " WHERE tutor_id = :id";
        return $this->db->execute($sql, $params) >= 0;
    }

    public function delete(int $id): bool
    {
        return $this->db->execute("DELETE FROM tutors WHERE tutor_id = :id", ['id' => $id]) > 0;
    }

    public function countAll(): int
    {
        return (int)$this->db->selectOne("SELECT COUNT(*) AS c FROM tutors")['c'];
    }

    /** FR-14: submit a daily class card / income report */
    public function submitReport(int $tutorId, array $data): int
    {
        $sql = "INSERT INTO tutor_reports (tutor_id, report_date, subject, students_count, income, notes)
                VALUES (:tutor_id, :report_date, :subject, :students_count, :income, :notes)";
        return (int)$this->db->insert($sql, [
            'tutor_id'       => $tutorId,
            'report_date'    => $data['report_date'],
            'subject'        => $data['subject'],
            'students_count' => $data['students_count'],
            'income'         => $data['income'],
            'notes'          => $data['notes'] ?? null,
        ]);
    }

    public function getReports(int $tutorId): array
    {
        return $this->db->select(
            "SELECT * FROM tutor_reports WHERE tutor_id = :id ORDER BY report_date DESC",
            ['id' => $tutorId]
        );
    }
}
