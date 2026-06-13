<?php
session_start();
// ── BACKEND: Redirect if already logged in
 if (isset($_SESSION['user_id'])) { header("Location: /pages/dashboard.php"); exit(); }

$error = null;
$successMsg = null;
if (isset($_SESSION['password_reset_success'])) {
    $successMsg = "Password reset successfully! Login with your new password.";
    unset($_SESSION['password_reset_success']);
}

// ── BACKEND: Handle login form submission
 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../classes/User.php';
    $user   = new User();
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($user->login($email, $password)) {
      header("Location: /pages/dashboard.php");
      exit();
     } 
    else {
      $error = "Invalid email or password.";
    }
 }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — Snip</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="auth-wrapper">
  <div class="auth-card fade-up">

    <div class="auth-logo">
      <div class="logo-icon">✂</div>
      <h2>Welcome back</h2>
      <p>Sign in to manage your links</p>
    </div>

    <!-- ── BACKEND: Echo $error here -->
    <?php if ($error): ?>
      <div class="alert alert-danger">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($successMsg): ?>
      <div class="alert alert-success">✓ <?= htmlspecialchars($successMsg) ?></div>
    <?php endif; ?>

    <div class="card">
      <form method="POST" action="login.php">

        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control" placeholder="you@example.com" required autofocus>
        </div>

        <div class="form-group">
          <label class="form-label">Password</label>
          <div style="position:relative;">
            <input type="password" name="password" class="form-control" id="loginPass" placeholder="Your password" required>
            <button type="button" class="copy-btn" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);" onclick="togglePassword('loginPass', this)">👁</button>
          </div>
          <div style="text-align:right; margin-top:6px; margin-bottom:10px;">
            <a href="forgot-password.php" style="font-size:13px; color:var(--accent);"> Forgot password? </a>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg">
          Sign In
        </button>

      </form>
    </div>

    <div class="auth-footer">
      Don't have an account? <a href="register.php">Sign up free</a>
    </div>

  </div>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>