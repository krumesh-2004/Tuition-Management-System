<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Database
 * Singleton wrapper around PDO providing a single, reusable
 * database connection and small set of helper query methods.
 * Encapsulation: connection details are private to this class.
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $connection;

    // Private constructor -> prevents creating multiple instances (Singleton pattern)
    private function __construct()
    {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Database Connection Failed: " . htmlspecialchars($e->getMessage()));
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /** Run a prepared SELECT and return all rows */
    public function select(string $sql, array $params = []): array
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Run a prepared SELECT and return a single row (or null) */
    public function selectOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /** Run an INSERT/UPDATE/DELETE and return affected row count */
    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /** Run an INSERT and return the new row's ID */
    public function insert(string $sql, array $params = []): string
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $this->connection->lastInsertId();
    }

    // Prevent cloning and unserializing of the singleton
    private function __clone() {}
    public function __wakeup() {}
}
