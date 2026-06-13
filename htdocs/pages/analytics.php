<?php
// ── BACKEND: Auth guard
require_once '../includes/auth-guard.php';
require_once '../classes/Link.php';
require_once '../classes/Click.php';

// ── BACKEND: Get link ID from URL, verify ownership
$linkId = intval($_GET['id'] ?? 0);
$linkObj = new Link();
$link    = $linkObj->getByIdAndUser($linkId, $_SESSION['user_id']);
if (!$link) { header("Location: dashboard.php"); exit(); }

// ── BACKEND: Get analytics data
$clickObj  = new Click();
$analytics = $clickObj->getAnalytics($linkId);
$total     = $analytics['total'];
$byBrowser = $analytics['by_browser'];  // ['Chrome'=>40, 'Firefox'=>10, ...]
$byDevice  = $analytics['by_device'];   // ['desktop'=>35, 'mobile'=>15, ...]
$recent    = $analytics['recent'];      // last 10 click rows

?>
<?php require_once '../includes/header.php'; ?>

<div class="dashboard-layout">
  <aside class="sidebar">
    <a href="dashboard.php"   class="sidebar-item"><span class="icon">🔗</span> My Links</a>
    <a href="create-link.php" class="sidebar-item"><span class="icon">➕</span> New Link</a>
    <div class="sidebar-section-label">Account</div>
    <a href="../logout.php"   class="sidebar-item"><span class="icon">🚪</span> Logout</a>
  </aside>

  <main class="main-content">

    <!-- Link Info Header -->
    <div class="analytics-header fade-up">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;">
        <div>
          <h4>Analytics for</h4>
          <h2 style="margin:6px 0;">
            <?= htmlspecialchars($link['title'] ?: $link['short_code']) ?>
          </h2>
          <div class="url-box" style="max-width:420px;margin-top:8px;">
            <span class="url-text" id="analytics-url">
              <!-- ── BACKEND: Echo full short URL -->
              <?= htmlspecialchars("https://urlshort.kesug.com/go/index.php?c=" . $link['short_code']) ?>
            </span>
            <button class="copy-btn" onclick="copyUrl('analytics-url', this)" data-tooltip="Copy">📋</button>
          </div>
          <div style="margin-top:8px;">
            <small>Original: </small>
            <a href="<?= htmlspecialchars($link['original_url']) ?>" target="_blank" style="font-size:12px;color:var(--accent);">
              <?= htmlspecialchars($link['original_url']) ?>
            </a>
          </div>
        </div>
        <div>
          <div class="stat-card" style="text-align:center;min-width:120px;">
            <div class="stat-icon">👆</div>
            <!-- ── BACKEND: Echo $total -->
            <div class="stat-number"><?= $total ?></div>
            <div class="stat-label">Total Clicks</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Browser & Device Stats -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));" class="fade-up">

      <!-- Browser Breakdown -->
      <div class="card">
        <h3 style="margin-bottom:16px;">By Browser</h3>
        <?php if ($total > 0): ?>
          <?php foreach ($byBrowser as $browser => $count): ?>
            <?php $pct = $total > 0 ? round(($count / $total) * 100) : 0; ?>
            <div class="browser-bar">
              <div class="browser-bar-label">
                <span><?= htmlspecialchars($browser) ?></span>
                <span><?= $count ?> (<?= $pct ?>%)</span>
              </div>
              <div class="progress">
                <div class="progress-bar" style="width:<?= $pct ?>%"></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div style="text-align:center;padding:20px;color:var(--text3);font-size:13px;">No data yet</div>
        <?php endif; ?>
      </div>

      <!-- Device Breakdown -->
      <div class="card">
        <h3 style="margin-bottom:16px;">By Device</h3>
        <?php if ($total > 0): ?>
          <?php foreach ($byDevice as $device => $count): ?>
            <?php $pct = $total > 0 ? round(($count / $total) * 100) : 0; ?>
            <div class="browser-bar">
              <div class="browser-bar-label">
                <span><?= ucfirst(htmlspecialchars($device)) ?></span>
                <span><?= $count ?> (<?= $pct ?>%)</span>
              </div>
              <div class="progress">
                <div class="progress-bar" style="width:<?= $pct ?>%; background:linear-gradient(90deg,var(--accent2),var(--accent));"></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div style="text-align:center;padding:20px;color:var(--text3);font-size:13px;">No data yet</div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Recent Clicks Table -->
    <div class="fade-up">
      <h3 style="margin-bottom:14px;">Recent Clicks</h3>
      <?php if (empty($recent)): ?>
        <div class="empty-state" style="padding:40px 0;">
          <div class="empty-icon">👆</div>
          <h3>No clicks yet</h3>
          <p>Share your short link to start seeing click data here</p>
        </div>
      <?php else: ?>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>IP Address</th>
                <th>Browser</th>
                <th>Device</th>
                <th>Time</th>
              </tr>
            </thead>
            <tbody>
              <!-- ── BACKEND: Loop through $recent clicks -->
              <?php foreach ($recent as $i => $click): ?>
              <tr>
                <td><small><?= $i + 1 ?></small></td>
                <td><code style="font-size:12px;"><?= htmlspecialchars($click['ip_address']) ?></code></td>
                <td><?= htmlspecialchars($click['browser']) ?></td>
                <td>
                  <span class="badge <?= $click['device'] === 'mobile' ? 'badge-purple' : 'badge-blue' ?>">
                    <?= $click['device'] === 'mobile' ? '📱' : '🖥' ?>
                    <?= ucfirst(htmlspecialchars($click['device'])) ?>
                  </span>
                </td>
                <td><small><?= date('d M, H:i', strtotime($click['clicked_at'])) ?></small></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  </main>
</div>

<?php require_once '../includes/footer.php'; ?>