<?php
// ─────────────────────────────────────────
// Auth Guard — include at top of every
// protected page (dashboard, create, analytics)
// ─────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /pages/login.php");
    exit();
}
?>