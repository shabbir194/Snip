<?php
// ─────────────────────────────────────────
// Start session if not already started
// ─────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ─────────────────────────────────────────
// Determine current page for active nav state
// ─────────────────────────────────────────
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — Snip' : 'Snip — Smart URL Shortener' ?></title>
 <link rel="stylesheet" href="/assets/css/style.css?v=2">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="navbar-inner">

    <!-- Brand -->
    <a href="/index.php" class="navbar-brand">
      <div class="brand-icon">✂</div>
      Snip
    </a>

    <!-- Nav Links -->
    <div class="navbar-links">
      <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Logged in — show user name + dashboard + logout -->
     <span style="color:var(--text2); font-size:13px; white-space:nowrap;" class="nav-greeting">Hi, <?= htmlspecialchars($_SESSION['user_name']) ?> 👋</span>
        <a href="/pages/dashboard.php"
           class="btn btn-ghost btn-sm <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
          🔗 Dashboard
        </a>
        <a href="/pages/create-link.php"
           class="btn btn-primary btn-sm">
          ➕ New Link
        </a>
        <a href="/logout.php" class="btn btn-ghost btn-sm">Logout</a>

      <?php else: ?>
        <!-- Not logged in — show login + register -->
        <a href="/pages/login.php"
           class="btn btn-ghost btn-sm <?= $currentPage === 'login.php' ? 'active' : '' ?>">
          Login
        </a>
        <a href="/pages/register.php"
           class="btn btn-primary btn-sm">
          Get Started
        </a>

      <?php endif; ?>
    </div>

  </div>
</nav>