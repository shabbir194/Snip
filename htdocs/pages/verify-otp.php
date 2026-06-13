<?php
session_start();

// If no reset email in session — go back to forgot password
if (!isset($_SESSION['reset_email'])) {
    header("Location: /pages/forgot-password.php");
    exit();
}

require_once '../classes/PasswordReset.php';

$error = null;
$email = $_SESSION['reset_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');

    if (empty($otp) || strlen($otp) !== 6) {
        $error = "Please enter the 6-digit OTP.";
    } else {
        $reset = new PasswordReset();

        if ($reset->verifyOtp($email, $otp)) {
            // OTP correct — move to reset password page
            $_SESSION['reset_verified'] = true;
            header("Location: /pages/reset-password.php");
            exit();
        } else {
            $error = "Invalid or expired OTP. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify OTP — Snip</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="auth-wrapper">
  <div class="auth-card fade-up">

    <div class="auth-logo">
      <div class="logo-icon">✂</div>
      <h2>Enter OTP</h2>
      <p>We sent a 6-digit code to <strong style="color:var(--text)"><?= htmlspecialchars($email) ?></strong></p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
      <form method="POST" action="verify-otp.php">
        <div class="form-group">
          <label class="form-label">6-Digit OTP</label>
          <input
            type="text"
            name="otp"
            class="form-control"
            placeholder="Enter OTP"
            maxlength="6"
            style="font-size:1.5rem; letter-spacing:10px; text-align:center;"
            required autofocus>
          <div class="form-hint">OTP expires in 10 minutes</div>
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-lg">
          ✅ Verify OTP
        </button>
      </form>
    </div>

    <div class="auth-footer">
      Didn't receive it?
      <a href="forgot-password.php">Resend OTP</a>
    </div>

  </div>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>