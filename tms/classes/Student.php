<?php
require_once __DIR__ . '/User.php';

/**
 * Student
 * Handles registration, profile management and CRUD used by Admin.
 */
class Student extends User
{
    protected string $firstName = '';
    protected string $lastName = '';
    protected string $qrCode = '';

    public function getTableName(): string { return 'students'; }
    public function getIdColumn(): string { return 'student_id'; }
    public function getRole(): string { return 'student'; }

    public function findById(int $id): ?array
    {
        return $this->db->selectOne("SELECT * FROM students WHERE student_id = :id", ['id' => $id]);
    }

    public function findByEmail(string $email): ?array
    {
        return $this->db->selectOne("SELECT * FROM students WHERE email = :email", ['email' => $email]);
    }

    public function all(string $search = ''): array
    {
        if ($search !== '') {
            $like = "%{$search}%";
            return $this->db->select(
                "SELECT * FROM students
                 WHERE first_name LIKE :s OR last_name LIKE :s OR email LIKE :s OR stream LIKE :s
                 ORDER BY created_at DESC",
                ['s' => $like]
            );
        }
        return $this->db->select("SELECT * FROM students ORDER BY created_at DESC");
    }

    /**
     * Register a new student (FR-02 / UC-02).
     * Generates a unique QR code identifier tied to the student record.
     */
    public function register(array $data): int
    {
        $qrCode = 'QR-STU-' . strtoupper(bin2hex(random_bytes(4)));

        $sql = "INSERT INTO students
                (first_name, last_name, email, password, phone, birthday, parent_name, parent_phone,
                 stream, subject1, subject2, subject3, al_education, photo_path, qr_code, status)
                VALUES
                (:first_name, :last_name, :email, :password, :phone, :birthday, :parent_name, :parent_phone,
                 :stream, :subject1, :subject2, :subject3, :al_education, :photo_path, :qr_code, 'Active')";

        $id = $this->db->insert($sql, [
            'first_name'   => $data['first_name'],
            'last_name'    => $data['last_name'],
            'email'        => $data['email'],
            'password'     => self::hashPassword($data['password']),
            'phone'        => $data['phone'] ?? null,
            'birthday'     => $data['birthday'] ?? null,
            'parent_name'  => $data['parent_name'] ?? null,
            'parent_phone' => $data['parent_phone'] ?? null,
            'stream'       => $data['stream'] ?? null,
            'subject1'     => $data['subject1'] ?? null,
            'subject2'     => $data['subject2'] ?? null,
            'subject3'     => $data['subject3'] ?? null,
            'al_education' => $data['al_education'] ?? null,
            'photo_path'   => $data['photo_path'] ?? null,
            'qr_code'      => $qrCode,
        ]);

        return (int)$id;
    }

    /** Admin: add a student directly */
    public function adminAdd(array $data): int
    {
        return $this->register($data);
    }

    /** Admin/Student: update student record (FR-11 / FR-03) */
    public function update(int $id, array $data): bool
    {
        $fields = [
            'first_name', 'last_name', 'email', 'phone', 'birthday',
            'parent_name', 'parent_phone', 'stream', 'subject1', 'subject2',
            'subject3', 'al_education', 'status',
        ];
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

        $sql = "UPDATE students SET " . implode(', ', $setParts) . " WHERE student_id = :id";
        return $this->db->execute($sql, $params) >= 0;
    }

    /** Admin: delete student (FR-11) */
    public function delete(int $id): bool
    {
        return $this->db->execute("DELETE FROM students WHERE student_id = :id", ['id' => $id]) > 0;
    }

    public function countAll(): int
    {
        return (int)$this->db->selectOne("SELECT COUNT(*) AS c FROM students")['c'];
    }
}
