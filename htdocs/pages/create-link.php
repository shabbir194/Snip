<?php
require_once '../includes/auth-guard.php';
require_once '../classes/Link.php';

$error   = null;
$success = null;
$newCode = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url   = trim($_POST['url']   ?? '');
    $title = trim($_POST['title'] ?? '');

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $error = "Please enter a valid URL including https://";
    } else {
        $link    = new Link();
        $newCode = $link->create($_SESSION['user_id'], $url, $title);
        if ($newCode) $success = true;
        else $error = "Could not create link. Try again.";
    }
}
?>
<?php require_once '../includes/header.php'; ?>

<div class="dashboard-layout">
  <aside class="sidebar">
    <a href="dashboard.php"   class="sidebar-item"><span class="icon">🔗</span> My Links</a>
    <a href="create-link.php" class="sidebar-item active"><span class="icon">➕</span> New Link</a>
    <div class="sidebar-section-label">Account</div>
    <a href="../logout.php"   class="sidebar-item"><span class="icon">🚪</span> Logout</a>
  </aside>

  <main class="main-content">
    <div style="max-width:560px;">

      <div class="page-header">
        <h2>Create New Link</h2>
        <p>Shorten a URL and start tracking clicks immediately</p>
      </div>

      <?php if ($success && $newCode): ?>
        <div class="card fade-up" style="margin-bottom:20px; border-color:var(--success);">
          <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
            <span style="font-size:1.4rem;">✅</span>
            <h3 style="color:var(--success);">Link Created!</h3>
          </div>
          <div class="url-box">
            <span class="url-text" id="new-url">
              <?= htmlspecialchars("https://urlshort.kesug.com/go/index.php?c=" . $newCode) ?>
            </span>
            <button class="copy-btn"
                    onclick="copyUrl('new-url', this)"
                    data-tooltip="Copy link">📋
            </button>
          </div>
          <div style="display:flex; gap:10px; margin-top:14px;">
            <a href="dashboard.php"   class="btn btn-secondary btn-sm">← All Links</a>
            <a href="create-link.php" class="btn btn-primary btn-sm">➕ Shorten Another</a>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="alert alert-danger">⚠ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="card fade-up">
        <form method="POST" action="create-link.php" id="createForm">

          <div class="form-group">
            <label class="form-label">Destination URL *</label>
            <input
              type="url"
              name="url"
              class="form-control"
              placeholder="https://www.youtube.com/watch?v=..."
              value="<?= htmlspecialchars($_POST['url'] ?? '') ?>"
              required>
            <div class="form-hint">Must include https:// or http://</div>
          </div>

          <div class="form-group">
            <label class="form-label">
              Link Title <span style="color:var(--text3)">(optional)</span>
            </label>
            <input
              type="text"
              name="title"
              class="form-control"
              placeholder="e.g. My Portfolio Link"
              maxlength="200"
              value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
            <div class="form-hint">A label to help you identify this link in the dashboard</div>
          </div>

          <div style="text-align:center; margin-top:8px;">
              <button type="submit" class="btn btn-primary" id="createBtn"
                 style="padding:10px 32px; font-size:14px;">
                 ✂ Shorten URL
             </button>
         </div>

        </form>
      </div>

    </div>
  </main>
</div>

<?php require_once '../includes/footer.php'; ?>