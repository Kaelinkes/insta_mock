<?php
require_once 'includes/functions.php';
$token = $_GET['token'] ?? '';
$error = $success = null;

if ($token) {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE reset_token=? AND reset_expires > NOW()");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$user) $error = "Invalid or expired token.";
} else {
    $error = "No token provided.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($user)) {
    $newpass = $_POST['password'] ?? '';
    if (strlen($newpass) < 4) {
        $error = "Password must be at least 4 characters.";
    } else {
        $hash = password_hash($newpass, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("UPDATE users SET password=?, reset_token=NULL, reset_expires=NULL WHERE id=?");
        $stmt->bind_param('si', $hash, $user['id']);
        if ($stmt->execute()) {
            $success = "Password reset successful. <a href='index.php'>Login</a>";
        } else {
            $error = "Failed to update password.";
        }
        $stmt->close();
    }
}

include 'includes/header.php';
?>
<div class="auth-card">
  <h2>Reset password</h2>
  <?php if ($error): ?><div class="alert"><?php echo e($error); ?></div><?php endif; ?>
  <?php if ($success): ?><div class="success"><?php echo $success; ?></div><?php endif; ?>
  <?php if (empty($success) && empty($error) || (empty($success) && isset($user))): ?>
  <form method="post">
    <label>New password<input type="password" name="password" required></label>
    <button type="submit">Set new password</button>
  </form>
  <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
