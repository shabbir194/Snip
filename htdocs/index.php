<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: /pages/dashboard.php");
    exit();
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) {
    header("Location: /pages/register.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Snip — Smart URL Shortener</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    /* ── How It Works ───────────────────────── */
    .how-it-works {
      padding: 70px 0;
      background: var(--bg2);
      border-top: 1px solid var(--border);
      border-bottom: 1px solid var(--border);
    }
    .how-it-works h4 {
      text-align: center;
      margin-bottom: 8px;
    }
    .how-it-works h2 {
      text-align: center;
      margin-bottom: 48px;
    }
    .steps-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 0;
      position: relative;
      max-width: 860px;
      margin: 0 auto;
    }
    .step-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      padding: 0 32px;
      position: relative;
    }
    .step-item:not(:last-child)::after {
      content: '→';
      position: absolute;
      right: -10px;
      top: 28px;
      font-size: 1.4rem;
      color: var(--text3);
    }
    .step-number {
      width: 56px;
      height: 56px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4rem;
      font-weight: 700;
      color: #fff;
      margin-bottom: 16px;
      flex-shrink: 0;
    }
    .step-item h3 {
      margin-bottom: 8px;
      font-size: 1rem;
    }
    .step-item p {
      font-size: 13px;
      line-height: 1.6;
    }
    @media (max-width: 600px) {
      .steps-grid { grid-template-columns: 1fr; gap: 32px; }
      .step-item:not(:last-child)::after { content: '↓'; right: 50%; top: auto; bottom: -24px; transform: translateX(50%); }
    }

    /* ── Stats Bar ──────────────────────────── */
    .stats-bar {
      padding: 40px 0;
      border-bottom: 1px solid var(--border);
    }
    .stats-bar-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
      max-width: 600px;
      margin: 0 auto;
      text-align: center;
    }
    .stats-bar-item .num {
      font-size: 2rem;
      font-weight: 700;
      color: var(--accent);
      line-height: 1;
      margin-bottom: 4px;
    }
    .stats-bar-item .lbl {
      font-size: 12px;
      color: var(--text3);
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="navbar-inner">
    <a href="index.php" class="navbar-brand">
      <div class="brand-icon">✂</div>
      Snip
    </a>
    <div class="navbar-links">
      <?php if (isset($_SESSION['user_id'])): ?>
        <span style="color:var(--text2); font-size:13px;">
          Hi, <?= htmlspecialchars($_SESSION['user_name']) ?> 👋
        </span>
        <a href="pages/dashboard.php"   class="btn btn-ghost btn-sm">🔗 Dashboard</a>
        <a href="pages/create-link.php" class="btn btn-primary btn-sm">➕ New Link</a>
        <a href="logout.php"            class="btn btn-ghost btn-sm">Logout</a>
      <?php else: ?>
        <a href="pages/login.php"    class="btn btn-ghost btn-sm">Login</a>
        <a href="pages/register.php" class="btn btn-primary btn-sm">Get Started</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- HERO -->
<section class="hero fade-up">
  <div class="container">
    <div class="hero-badge">✨ Free URL Shortener with Analytics</div>
    <h1>Shorten links.<br><span>Track every click.</span></h1>
    <p>Turn long ugly URLs into clean short links. Know who clicked, when, and from which device.</p>

    <div class="shorten-form-wrap fade-up-2">
      <form method="POST" action="index.php" id="shortenForm">
        <div class="shorten-input-row">
          <input
            type="url"
            name="url"
            class="form-control"
            placeholder="Paste your long URL here..."
            required>
          <button type="submit" class="btn btn-primary">✂ Shorten</button>
        </div>
        <p style="margin-top:10px; font-size:12px; text-align:left;">
          Want to save & track your links?
          <a href="pages/register.php" style="color:var(--accent);">Create a free account →</a>
        </p>
      </form>
    </div>
  </div>
</section>

<!-- STATS BAR -->
<section class="stats-bar">
  <div class="container">
    <div class="stats-bar-grid">
      <div class="stats-bar-item">
        <div class="num">100%</div>
        <div class="lbl">Free Forever</div>
      </div>
      <div class="stats-bar-item">
        <div class="num">Fast</div>
        <div class="lbl">Instant Redirect</div>
      </div>
      <div class="stats-bar-item">
        <div class="num">🔒</div>
        <div class="lbl">Secure & Private</div>
      </div>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="how-it-works fade-up">
  <div class="container">
    <h4>Simple Process</h4>
    <h2>How it works</h2>
    <div class="steps-grid">

      <div class="step-item">
        <div class="step-number">1</div>
        <h3>Paste your URL</h3>
        <p>Copy any long URL — YouTube, Amazon, Google Docs, anything — and paste it into the box above.</p>
      </div>

      <div class="step-item">
        <div class="step-number">2</div>
        <h3>Get short link</h3>
        <p>Snip instantly generates a clean short link for you. Copy it with one click and it's ready to share.</p>
      </div>

      <div class="step-item">
        <div class="step-number">3</div>
        <h3>Track every click</h3>
        <p>Every time someone clicks your link, Snip records the browser, device, and time — all in your dashboard.</p>
      </div>

    </div>
  </div>
</section>

<!-- FEATURES -->
<section class="features fade-up-3">
  <div class="container">
    <h4 style="text-align:center; margin-bottom:8px;">Why Snip?</h4>
    <h2 style="text-align:center;">Everything you need, nothing you don't</h2>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">⚡</div>
        <h3>Instant Shortening</h3>
        <p>Paste a URL and get a short link in under a second.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">📊</div>
        <h3>Click Analytics</h3>
        <p>Track total clicks, browser type, and device per link.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🔒</div>
        <h3>Secure & Private</h3>
        <p>Your links are yours. No data sold, no ads injected.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🗂</div>
        <h3>Link Dashboard</h3>
        <p>Manage all your links in one clean organized place.</p>
      </div>
    </div>
  </div>
</section>

<!-- CTA SECTION -->
<section style="padding:70px 0; text-align:center;">
  <div class="container">
    <h2 style="margin-bottom:12px;">Ready to start shortening?</h2>
    <p style="margin-bottom:28px; font-size:1rem;">
      Create a free account and get your first short link in 60 seconds.
    </p>
    <a href="pages/register.php" class="btn btn-primary btn-lg">
      🚀 Create Free Account
    </a>
    <p style="margin-top:14px; font-size:12px; color:var(--text3);">
      No credit card required · Free forever
    </p>
  </div>
</section>

<!-- FOOTER -->
<footer style="border-top:1px solid var(--border); padding:28px 0;">
  <div class="container" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
    <div class="navbar-brand">
      <div class="brand-icon" style="width:24px; height:24px; font-size:11px;">✂</div>
      Snip
    </div>
    <p style="font-size:13px;">Built with PHP + MySQL · Internship Project</p>
  </div>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>