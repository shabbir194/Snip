<?php

require_once __DIR__ . '/../config/database.php';

class Database {

    // ─────────────────────────────────────────
    // PROPERTIES
    // ─────────────────────────────────────────
    private static $instance = null;
    private $pdo;

    // ─────────────────────────────────────────
    // CONSTRUCTOR — private so no one can do new Database()
    // ─────────────────────────────────────────
    private function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE,        PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────
    // GET INSTANCE — the only way to get this class
    // ─────────────────────────────────────────
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // ─────────────────────────────────────────
    // GET CONNECTION — returns PDO object
    // ─────────────────────────────────────────
    public function getConnection(): PDO {
        return $this->pdo;
    }
}
?>