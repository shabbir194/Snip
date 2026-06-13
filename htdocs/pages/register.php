<?php
session_start();
if (isset($_SESSION['user_id'])) { 
    header("Location: /pages/dashboard.php"); 
    exit(); 
}

$error   = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../classes/User.php';
    $user     = new User();
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $result = $user->register($name, $email, $password);
        if ($result === 'success') {
             header("Location: /pages/login.php");
             exit();
        } elseif ($result === 'duplicate') {
            $error = "This email is already registered.";
        } else {
            $error = "Something went wrong. Try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register — Snip</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="auth-wrapper">
  <div class="auth-card fade-up">

    <div class="auth-logo">
      <div class="logo-icon">✂</div>
      <h2>Create your account</h2>
      <p>Start shortening and tracking links for free</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
      <form method="POST" action="register.php" id="registerForm">

        <div class="form-group">
          <label class="form-label">Full Name</label>
          <input type="text" name="name" class="form-control"
                 placeholder="Your name"
                 value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                 required>
        </div>

        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control"
                 placeholder="you@example.com"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                 required>
        </div>

        <div class="form-group">
          <label class="form-label">Password</label>
          <div style="position:relative;">
            <input type="password" name="password" class="form-control"
                   id="password" placeholder="Min. 6 characters" required>
            <button type="button" class="copy-btn"
                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);"
                    onclick="togglePassword('password', this)">👁
            </button>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Confirm Password</label>
          <input type="password" name="confirm_password" class="form-control"
                 id="confirm_password" placeholder="Repeat password" required>
          <div class="form-error" id="pass-match-error" style="display:none;">
            Passwords do not match
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg" id="registerBtn">
          Create Account
        </button>

      </form>
    </div>

    <div class="auth-footer">
      Already have an account? <a href="login.php">Sign in</a>
    </div>

  </div>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>