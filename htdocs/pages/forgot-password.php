<?php
session_start();

require_once '../classes/PasswordReset.php';
require_once '../classes/Mailer.php';

$error   = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $reset = new PasswordReset();

        if (!$reset->emailExists($email)) {
            // Don't reveal if email exists or not — security best practice
            $success = "If this email is registered, an OTP has been sent to it.";
        } else {
            $otp  = $reset->generateOtp();
            $name = $reset->getNameByEmail($email);
            $reset->saveOtp($email, $otp);
            $sent = Mailer::sendOtp($email, $name, $otp);

            if ($sent) {
                // Store email in session for next step
                $_SESSION['reset_email'] = $email;
                header("Location: /pages/verify-otp.php");
                exit();
            } else {
                $error = "Failed to send OTP. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password — Snip</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="auth-wrapper">
  <div class="auth-card fade-up">

    <div class="auth-logo">
      <div class="logo-icon">✂</div>
      <h2>Forgot Password</h2>
      <p>Enter your email and we'll send you an OTP</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="card">
      <form method="POST" action="forgot-password.php">
        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input
            type="email"
            name="email"
            class="form-control"
            placeholder="you@example.com"
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
            required autofocus>
          <div class="form-hint">We'll send a 6-digit OTP to this email</div>
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-lg">
          📧 Send OTP
        </button>
      </form>
    </div>

    <div class="auth-footer">
      Remember your password? <a href="login.php">Sign in</a>
    </div>

  </div>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>