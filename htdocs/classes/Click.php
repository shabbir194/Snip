<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Database.php';

class Click {

    private $pdo;

    // ─────────────────────────────────────────
    // CONSTRUCTOR
    // ─────────────────────────────────────────
    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    // ─────────────────────────────────────────
    // PARSE BROWSER — private helper
    // ─────────────────────────────────────────
    private function parseBrowser(string $agent): string {
        if (str_contains($agent, 'Edg'))     return 'Edge';
        if (str_contains($agent, 'Chrome'))  return 'Chrome';
        if (str_contains($agent, 'Firefox')) return 'Firefox';
        if (str_contains($agent, 'Safari'))  return 'Safari';
        if (str_contains($agent, 'Opera'))   return 'Opera';
        return 'Other';
    }

    // ─────────────────────────────────────────
    // PARSE DEVICE — private helper
    // ─────────────────────────────────────────
    private function parseDevice(string $agent): string {
        if (str_contains($agent, 'Mobile'))  return 'mobile';
        if (str_contains($agent, 'Tablet'))  return 'tablet';
        return 'desktop';
    }

    // ─────────────────────────────────────────
    // LOG — record a click event
    // ─────────────────────────────────────────
    public function log(int $linkId): void {
        $ip      = $_SERVER['REMOTE_ADDR']      ?? '0.0.0.0';
        $agent   = $_SERVER['HTTP_USER_AGENT']  ?? '';
        $referrer = $_SERVER['HTTP_REFERER']    ?? '';
        $browser = $this->parseBrowser($agent);
        $device  = $this->parseDevice($agent);

        $stmt = $this->pdo->prepare("
            INSERT INTO clicks (link_id, ip_address, browser, device, referrer, clicked_at)
            VALUES (:link_id, :ip, :browser, :device, :referrer, NOW())
        ");

        $stmt->execute([
            ':link_id'  => $linkId,
            ':ip'       => $ip,
            ':browser'  => $browser,
            ':device'   => $device,
            ':referrer' => $referrer,
        ]);
    }

    // ─────────────────────────────────────────
    // GET TOTAL BY LINK
    // ─────────────────────────────────────────
    public function getTotalByLink(int $linkId): int {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM clicks WHERE link_id = :link_id
        ");
        $stmt->execute([':link_id' => $linkId]);
        return (int) $stmt->fetchColumn();
    }

    // ─────────────────────────────────────────
    // GET ANALYTICS — for analytics page
    // ─────────────────────────────────────────
    public function getAnalytics(int $linkId): array {

        // Total clicks
        $total = $this->getTotalByLink($linkId);

        // By browser
        $stmt = $this->pdo->prepare("
            SELECT browser, COUNT(*) AS count
            FROM clicks
            WHERE link_id = :link_id
            GROUP BY browser
            ORDER BY count DESC
        ");
        $stmt->execute([':link_id' => $linkId]);
        $byBrowserRaw = $stmt->fetchAll();

        $byBrowser = [];
        foreach ($byBrowserRaw as $row) {
            $byBrowser[$row['browser']] = (int) $row['count'];
        }

        // By device
        $stmt = $this->pdo->prepare("
            SELECT device, COUNT(*) AS count
            FROM clicks
            WHERE link_id = :link_id
            GROUP BY device
            ORDER BY count DESC
        ");
        $stmt->execute([':link_id' => $linkId]);
        $byDeviceRaw = $stmt->fetchAll();

        $byDevice = [];
        foreach ($byDeviceRaw as $row) {
            $byDevice[$row['device']] = (int) $row['count'];
        }

        // Recent 10 clicks
        $stmt = $this->pdo->prepare("
            SELECT ip_address, browser, device, referrer, clicked_at
            FROM clicks
            WHERE link_id = :link_id
            ORDER BY clicked_at DESC
            LIMIT 10
        ");
        $stmt->execute([':link_id' => $linkId]);
        $recent = $stmt->fetchAll();

        return [
            'total'      => $total,
            'by_browser' => $byBrowser,
            'by_device'  => $byDevice,
            'recent'     => $recent,
        ];
    }
}