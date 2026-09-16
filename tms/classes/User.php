<?php
require_once __DIR__ . '/Database.php';

/**
 * Abstract class User
 * Base class for Admin, Student and Tutor.
 * Demonstrates: Abstraction (abstract methods each child must implement),
 * Encapsulation (protected properties, getters/setters),
 * Inheritance (Admin/Student/Tutor extend this class).
 */
abstract class User
{
    protected int $id = 0;
    protected string $name = '';
    protected string $email = '';
    protected string $password = '';
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Abstract methods - every concrete user type must define its own table & dashboard route
    abstract public function getTableName(): string;
    abstract public function getIdColumn(): string;
    abstract public function getRole(): string;

    // ---------- Getters / Setters (Encapsulation) ----------
    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function setName(string $name): void { $this->name = trim($name); }
    public function setEmail(string $email): void { $this->email = trim($email); }

    /**
     * Shared login logic used by Admin, Student and Tutor.
     * Looks the user up by email in the relevant table, then verifies password.
     */
    public function login(string $email, string $password): ?array
    {
        $table = $this->getTableName();
        $sql = "SELECT * FROM {$table} WHERE email = :email LIMIT 1";
        $row = $this->db->selectOne($sql, ['email' => $email]);

        if ($row && password_verify($password, $row['password'])) {
            return $row;
        }
        return null;
    }

    /** Hash a plain-text password for storage */
    public static function hashPassword(string $plain): string
    {
        return password_hash($plain, PASSWORD_BCRYPT);
    }

    /** Clear the session (shared logout behaviour) */
    public static function logout(): void
    {
        session_unset();
        session_destroy();
    }
}
