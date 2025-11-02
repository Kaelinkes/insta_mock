<?php
require_once 'includes/functions.php';
if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (!$name || !$email || !$password) $errors[] = "Please fill all required fields.";
    if ($password !== $password2) $errors[] = "Passwords do not match.";
    if (find_user_by_email($email)) $errors[] = "An account with that email already exists.";

    $profile = 'uploads/default_profile.png';
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] !== UPLOAD_ERR_NO_FILE) {
        $saved = save_image_upload($_FILES['profile_pic']);
        if ($saved) $profile = $saved;
        else $errors[] = "Profile image upload failed or invalid.";
    }

    if (empty($errors)) {
        if (register_user($name, $email, $password, $profile)) {
            $user = find_user_by_email($email);
            $_SESSION['user_id'] = $user['id'];
            header('Location: dashboard.php');
            exit;
        } else $errors[] = "Registration failed, try again.";
    }
}
include 'includes/header.php';
?>
<div class="auth-card">
  <h2>Create account</h2>
  <?php if ($errors): foreach ($errors as $err): ?>
    <div class="alert"><?php echo e($err); ?></div>
  <?php endforeach; endif; ?>
  <form method="post" enctype="multipart/form-data">
    <label>Full name<input name="full_name" required></label>
    <label>Email<input type="email" name="email" required></label>
    <label>Password<input type="password" name="password" required></label>
    <label>Confirm password<input type="password" name="password2" required></label>
    <label>Profile picture (optional)<input type="file" name="profile_pic" accept="image/*" id="profile-input"></label>
    <div id="profile-preview" class="img-preview"></div>
    <button type="submit">Register</button>
    <p class="muted">Already have an account? <a href="index.php">Login</a></p>
  </form>
</div>
<?php include 'includes/footer.php'; ?>
