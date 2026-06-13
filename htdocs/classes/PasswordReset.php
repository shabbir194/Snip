<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Database.php';

class PasswordReset {

    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    // ─────────────────────────────────────────
    // GENERATE 6-DIGIT OTP
    // ─────────────────────────────────────────
    public function generateOtp(): string {
        return str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    // ─────────────────────────────────────────
    // SAVE OTP TO DB — expires in 10 minutes
    // ─────────────────────────────────────────
    public function saveOtp(string $email, string $otp): bool {
        // Delete any existing OTP for this email first
        $this->deleteOtp($email);

        $stmt = $this->pdo->prepare("
            INSERT INTO password_resets (email, otp, expires_at, created_at)
            VALUES (:email, :otp, DATE_ADD(NOW(), INTERVAL 10 MINUTE), NOW())
        ");
        $stmt->execute([
            ':email' => $email,
            ':otp'   => $otp,
        ]);
        return $stmt->rowCount() > 0;
    }

    // ─────────────────────────────────────────
    // VERIFY OTP
    // ─────────────────────────────────────────
    public function verifyOtp(string $email, string $otp): bool {
        $stmt = $this->pdo->prepare("
            SELECT * FROM password_resets
            WHERE email = :email
            AND otp = :otp
            AND expires_at > NOW()
        ");
        $stmt->execute([
            ':email' => $email,
            ':otp'   => $otp,
        ]);
        return $stmt->fetch() !== false;
    }

    // ─────────────────────────────────────────
    // UPDATE PASSWORD
    // ─────────────────────────────────────────
    public function updatePassword(string $email, string $newPassword): bool {
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt   = $this->pdo->prepare("
            UPDATE users SET password = :password WHERE email = :email
        ");
        $stmt->execute([
            ':password' => $hashed,
            ':email'    => $email,
        ]);
        return $stmt->rowCount() > 0;
    }

    // ─────────────────────────────────────────
    // DELETE OTP after use or on new request
    // ─────────────────────────────────────────
    public function deleteOtp(string $email): void {
        $stmt = $this->pdo->prepare("
            DELETE FROM password_resets WHERE email = :email
        ");
        $stmt->execute([':email' => $email]);
    }

    // ─────────────────────────────────────────
    // CHECK IF EMAIL EXISTS IN USERS TABLE
    // ─────────────────────────────────────────
    public function emailExists(string $email): bool {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM users WHERE email = :email
        ");
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    // ─────────────────────────────────────────
    // GET USER NAME BY EMAIL
    // ─────────────────────────────────────────
    public function getNameByEmail(string $email): string {
        $stmt = $this->pdo->prepare("
            SELECT name FROM users WHERE email = :email
        ");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ? $row['name'] : 'User';
    }
}