<?php
session_start();

// Must have completed both previous steps
if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_verified'])) {
    header("Location: /pages/forgot-password.php");
    exit();
}

require_once '../classes/PasswordReset.php';

$error   = null;
$email   = $_SESSION['reset_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password']         ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $reset = new PasswordReset();
        $reset->updatePassword($email, $password);
        $reset->deleteOtp($email);

        // Clear session reset data
        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_verified']);

        // Redirect to login with success message
        $_SESSION['password_reset_success'] = true;
        header("Location: /pages/login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password — Snip</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="auth-wrapper">
  <div class="auth-card fade-up">

    <div class="auth-logo">
      <div class="logo-icon">✂</div>
      <h2>Set New Password</h2>
      <p>Create a strong new password for your account</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
      <form method="POST" action="reset-password.php" id="resetForm">

        <div class="form-group">
          <label class="form-label">New Password</label>
          <div style="position:relative;">
            <input
              type="password"
              name="password"
              class="form-control"
              id="password"
              placeholder="Min. 6 characters"
              required>
            <button type="button" class="copy-btn"
                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);"
                    onclick="togglePassword('password', this)">👁
            </button>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Confirm New Password</label>
          <input
            type="password"
            name="confirm_password"
            class="form-control"
            id="confirm_password"
            placeholder="Repeat password"
            required>
          <div class="form-error" id="pass-match-error" style="display:none;">
            Passwords do not match
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg" id="registerBtn">
          🔒 Reset Password
        </button>

      </form>
    </div>

  </div>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>