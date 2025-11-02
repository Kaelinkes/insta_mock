<?php
require_once 'includes/functions.php';
if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$email || !$password) $errors[] = "Please enter both email and password.";
    else {
        $u = find_user_by_email($email);
        if ($u && password_verify($password, $u['password'])) {
            $_SESSION['user_id'] = $u['id'];
            header('Location: dashboard.php');
            exit;
        } else $errors[] = "Invalid credentials.";
    }
}

include 'includes/header.php';
?>
<div class="auth-card">
  <h2>Welcome back</h2>
  <?php if ($errors): foreach ($errors as $err): ?>
    <div class="alert"><?php echo e($err); ?></div>
  <?php endforeach; endif; ?>
  <form method="post" class="auth-form">
    <label>Email<input type="email" name="email" required></label>
    <label>Password<input type="password" name="password" required></label>
    <button type="submit">Login</button>
    <p class="muted"><a href="forgot.php">Forgot Password?</a> • <a href="register.php">Create account</a></p>
  </form>
</div>
<?php include 'includes/footer.php'; ?>
