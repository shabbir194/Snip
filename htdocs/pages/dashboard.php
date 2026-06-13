<?php
$pageTitle = "Dashboard";
require_once '../includes/auth-guard.php';
require_once '../classes/Link.php';

// ── Fetch all links for this user with click counts
$linkObj     = new Link();
$links       = $linkObj->getAllByUser($_SESSION['user_id']);
$totalLinks  = count($links);
$totalClicks = array_sum(array_column($links, 'total_clicks'));

// ── Handle delete form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['link_id'])) {
    $linkId = intval($_POST['link_id']);
    $linkObj->delete($linkId, $_SESSION['user_id']);
    header("Location: dashboard.php");
    exit();
}
?>

<?php require_once '../includes/header.php'; ?>

<div class="dashboard-layout">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <a href="dashboard.php"   class="sidebar-item active"><span class="icon">🔗</span> My Links</a>
    <a href="create-link.php" class="sidebar-item"><span class="icon">➕</span> New Link</a>
    <div class="sidebar-section-label">Account</div>
    <a href="forgot-password.php" class="sidebar-item"><span class="icon">🔑</span> Forgot Password</a>
    <a href="../logout.php"   class="sidebar-item"><span class="icon">🚪</span> Logout</a>
  </aside>

  <!-- MAIN CONTENT -->
  <main class="main-content">

    <!-- Stats -->
    <div class="stats-grid fade-up">
      <div class="stat-card">
        <div class="stat-icon">🔗</div>
        <div class="stat-number"><?= $totalLinks ?></div>
        <div class="stat-label">Total Links</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">👆</div>
        <div class="stat-number"><?= $totalClicks ?></div>
        <div class="stat-label">Total Clicks</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">📈</div>
        <div class="stat-number" id="best-link-clicks">—</div>
        <div class="stat-label">Best Link Clicks</div>
      </div>
    </div>

    <!-- Page Header -->
    <div class="page-header fade-up">
      <div class="page-header-inner">
        <div>
          <h2>My Links</h2>
          <p>All your shortened URLs in one place</p>
        </div>
        <a href="create-link.php" class="btn btn-primary">➕ New Link</a>
      </div>
    </div>

    <!-- Links Table -->
    <?php if (empty($links)): ?>
      <div class="empty-state fade-up">
        <div class="empty-icon">🔗</div>
        <h3>No links yet</h3>
        <p>Create your first short link to get started</p>
        <a href="create-link.php" class="btn btn-primary" style="margin-top:16px;">
          ➕ Create Link
        </a>
      </div>

    <?php else: ?>
      <div class="table-wrap fade-up">
        <table>
          <thead>
            <tr>
              <th>Short Link</th>
              <th>Original URL</th>
              <th>Clicks</th>
              <th>Created</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($links as $link): ?>
            <tr>
              <td>
                <div class="url-box" style="max-width:180px;">
                  <span class="url-text" id="url-<?= $link['id'] ?>">
                    <?= htmlspecialchars("https://urlshort.kesug.com/go/index.php?c=" . $link['short_code']) ?>
                  </span>
                  <button class="copy-btn"
                          onclick="copyUrl('url-<?= $link['id'] ?>', this)"
                          data-tooltip="Copy">📋
                  </button>
                </div>
              </td>
              <td>
                <div class="link-row-original"
                     data-tooltip="<?= htmlspecialchars($link['original_url']) ?>">
                  <?= htmlspecialchars($link['original_url']) ?>
                </div>
                <?php if (!empty($link['title'])): ?>
                  <div style="font-size:12px; color:var(--text2); margin-top:2px;">
                    <?= htmlspecialchars($link['title']) ?>
                  </div>
                <?php endif; ?>
              </td>
              <td>
                <span class="click-count"><?= $link['total_clicks'] ?></span>
              </td>
              <td>
                <small><?= date('d M Y', strtotime($link['created_at'])) ?></small>
              </td>
              <td>
                <?php if ($link['is_active']): ?>
                  <span class="badge badge-green">● Active</span>
                <?php else: ?>
                  <span class="badge badge-red">● Inactive</span>
                <?php endif; ?>
              </td>
              <td>
                <div style="display:flex; gap:6px;">
                  <a href="analytics.php?id=<?= $link['id'] ?>"
                     class="btn btn-secondary btn-sm"
                     data-tooltip="View Analytics">📊
                  </a>
                  <button class="btn btn-danger btn-sm"
                          onclick="confirmDelete(<?= $link['id'] ?>)"
                          data-tooltip="Delete">🗑
                  </button>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

  </main>
</div>

<!-- Delete Confirm Modal -->
<div class="modal-overlay" id="deleteModal">
  <div class="modal">
    <div class="modal-header">
      <h3>Delete Link</h3>
      <button class="modal-close" onclick="closeModal('deleteModal')">✕</button>
    </div>
    <p style="margin-bottom:20px;">
      Are you sure you want to delete this link? All click data will be lost permanently.
    </p>
    <div style="display:flex; gap:10px; justify-content:flex-end;">
      <button class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancel</button>
      <form method="POST" action="dashboard.php" id="deleteForm">
        <input type="hidden" name="link_id" id="deleteLinkId">
        <button type="submit" class="btn btn-danger">Delete</button>
      </form>
    </div>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>