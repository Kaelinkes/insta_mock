<?php
require_once 'includes/functions.php';
if (!empty($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }
$success = $error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $user = find_user_by_email($email);
    if ($user) {
        $token = generate_token();
        $expires = date('Y-m-d H:i:s', time() + 600); // 10 minutes
        global $mysqli;
        $stmt = $mysqli->prepare("UPDATE users SET reset_token=?, reset_expires=? WHERE id=?");
        $stmt->bind_param('ssi', $token, $expires, $user['id']);
        $stmt->execute();
        $stmt->close();
        // For local testing we show a link (lecturer can click). In production you'd email this.
        $link = 'reset.php?token=' . $token;
        $success = "A reset link (for local testing) has been generated: <a href=\"" . e($link) . "\">Reset password</a>. Token expires in 10 minutes.";
    } else {
        $error = "No account with that email was found.";
    }
}

include 'includes/header.php';
?>
<div class="auth-card">
  <h2>Forgot password</h2>
  <?php if ($success): ?><div class="success"><?php echo $success; ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert"><?php echo e($error); ?></div><?php endif; ?>
  <form method="post">
    <label>Email address<input type="email" name="email" required></label>
    <button type="submit">Generate reset link</button>
    <p class="muted"><a href="index.php">Back to login</a></p>
  </form>
</div>
<?php include 'includes/footer.php'; ?>
