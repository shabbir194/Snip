<?php

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Link.php';
require_once __DIR__ . '/../classes/Click.php';

// ─────────────────────────────────────────
// STEP 1 — Read the short code from URL
// ─────────────────────────────────────────
$code = trim($_GET['c'] ?? '');

// If no code in URL, redirect to homepage
if (empty($code)) {
    header("Location: /index.php");
    exit();
}

// ─────────────────────────────────────────
// STEP 2 — Find the link in database
// ─────────────────────────────────────────
$linkObj = new Link();
$link    = $linkObj->getByShortCode($code);

// If code not found or link is inactive, redirect to homepage with error
if (!$link) {
    header("Location: /index.php?error=notfound");
    exit();
}

// ─────────────────────────────────────────
// STEP 3 — Log the click
// ─────────────────────────────────────────
$clickObj = new Click();
$clickObj->log($link['id']);

// ─────────────────────────────────────────
// STEP 4 — Redirect visitor to original URL
// ─────────────────────────────────────────
header("Location: " . $link['original_url'], true, 302);
exit();