<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Database.php';

class User {

    private $pdo;

    // ─────────────────────────────────────────
    // CONSTRUCTOR
    // ─────────────────────────────────────────
    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    // ─────────────────────────────────────────
    // REGISTER
    // ─────────────────────────────────────────
    public function register(string $name, string $email, string $password): string {

        // Step 1 — Check if email already exists
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            return 'duplicate';
        }

        // Step 2 — Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Step 3 — Insert new user into DB
        $stmt = $this->pdo->prepare("
            INSERT INTO users (name, email, password, created_at)
            VALUES (:name, :email, :password, NOW())
        ");

        $stmt->execute([
            ':name'     => $name,
            ':email'    => $email,
            ':password' => $hashedPassword,
        ]);

        return 'success';
    }

    // ─────────────────────────────────────────
    // LOGIN
    // ─────────────────────────────────────────
    public function login(string $email, string $password): bool {

        // Step 1 — Find user by email
        $stmt = $this->pdo->prepare("
            SELECT id, name, password FROM users WHERE email = :email LIMIT 1
        ");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Step 2 — If no user found, return false
        if (!$user) {
            return false;
        }

        // Step 3 — Verify password against stored hash
        if (!password_verify($password, $user['password'])) {
            return false;
        }

        // Step 4 — Password correct — start session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        return true;
    }

    // ─────────────────────────────────────────
    // LOGOUT
    // ─────────────────────────────────────────
    public function logout(): void {
        session_start();
        session_unset();
        session_destroy();
        header("Location: /pages/login.php");
        exit();
    }

    // ─────────────────────────────────────────
    // IS LOGGED IN
    // ─────────────────────────────────────────
    public function isLoggedIn(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }

    // ─────────────────────────────────────────
    // GET CURRENT USER ID
    // ─────────────────────────────────────────
    public function getCurrentUserId(): int {
        return $_SESSION['user_id'] ?? 0;
    }

    // ─────────────────────────────────────────
    // GET CURRENT USER NAME
    // ─────────────────────────────────────────
    public function getCurrentUserName(): string {
        return $_SESSION['user_name'] ?? 'User';
    }
}
