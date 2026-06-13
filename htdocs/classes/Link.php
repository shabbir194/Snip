<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Database.php';

class Link {

    private $pdo;

    // ─────────────────────────────────────────
    // CONSTRUCTOR
    // ─────────────────────────────────────────
    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    // ─────────────────────────────────────────
    // GENERATE SHORT CODE — private helper
    // ─────────────────────────────────────────
    private function generateShortCode(int $length = 6): string {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        do {
            $code = substr(str_shuffle($chars), 0, $length);
        } while ($this->shortCodeExists($code));
        return $code;
    }

    // ─────────────────────────────────────────
    // CHECK IF SHORT CODE ALREADY EXISTS — private helper
    // ─────────────────────────────────────────
    private function shortCodeExists(string $code): bool {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM links WHERE short_code = :code
        ");
        $stmt->execute([':code' => $code]);
        return $stmt->fetchColumn() > 0;
    }

    // ─────────────────────────────────────────
    // CREATE — shorten a new URL
    // ─────────────────────────────────────────
    public function create(int $userId, string $originalUrl, string $title = ''): string|false {
        try {
            $shortCode = $this->generateShortCode();

            $stmt = $this->pdo->prepare("
                INSERT INTO links (user_id, original_url, short_code, title, is_active, created_at)
                VALUES (:user_id, :original_url, :short_code, :title, 1, NOW())
            ");

            $stmt->execute([
                ':user_id'      => $userId,
                ':original_url' => $originalUrl,
                ':short_code'   => $shortCode,
                ':title'        => $title,
            ]);

            return $shortCode;

        } catch (PDOException $e) {
            return false;
        }
    }

    // ─────────────────────────────────────────
    // GET BY SHORT CODE — for redirect engine
    // ─────────────────────────────────────────
    public function getByShortCode(string $code): ?array {
        $stmt = $this->pdo->prepare("
            SELECT * FROM links
            WHERE short_code = :code
            AND is_active = 1
        ");
        $stmt->execute([':code' => $code]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    // ─────────────────────────────────────────
    // GET ALL BY USER — for dashboard
    // ─────────────────────────────────────────
    public function getAllByUser(int $userId): array {
        $stmt = $this->pdo->prepare("
            SELECT l.*,
                   COUNT(c.id) AS total_clicks
            FROM links l
            LEFT JOIN clicks c ON c.link_id = l.id
            WHERE l.user_id = :user_id
            GROUP BY l.id
            ORDER BY l.created_at DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────
    // GET BY ID AND USER — for analytics page
    // ─────────────────────────────────────────
    public function getByIdAndUser(int $linkId, int $userId): ?array {
        $stmt = $this->pdo->prepare("
            SELECT * FROM links
            WHERE id = :id
            AND user_id = :user_id
        ");
        $stmt->execute([
            ':id'      => $linkId,
            ':user_id' => $userId,
        ]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    // ─────────────────────────────────────────
    // DELETE — remove a link
    // ─────────────────────────────────────────
    public function delete(int $linkId, int $userId): bool {
        $stmt = $this->pdo->prepare("
            DELETE FROM links
            WHERE id = :id
            AND user_id = :user_id
        ");
        $stmt->execute([
            ':id'      => $linkId,
            ':user_id' => $userId,
        ]);
        return $stmt->rowCount() > 0;
    }
}