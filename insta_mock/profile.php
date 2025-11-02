<?php
require_once 'includes/functions.php';
if (empty($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$me = find_user_by_id($_SESSION['user_id']);
$view_id = intval($_GET['user_id'] ?? $me['id']);
$user = find_user_by_id($view_id);
if (!$user) { header('Location: dashboard.php'); exit; }

$editing = ($view_id === $me['id']);
$errors = []; $success = null;

if ($editing && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    if (!$name) $errors[] = "Name cannot be empty.";
    $profile_pic = $user['profile_pic'];
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] !== UPLOAD_ERR_NO_FILE) {
        $saved = save_image_upload($_FILES['profile_pic']);
        if ($saved) $profile_pic = $saved;
        else $errors[] = "Profile image upload failed.";
    }
    if (empty($errors)) {
        global $mysqli;
        $stmt = $mysqli->prepare("UPDATE users SET full_name=?, profile_pic=? WHERE id=?");
        $stmt->bind_param('ssi', $name, $profile_pic, $me['id']);
        if ($stmt->execute()) {
            $success = "Profile updated.";
            $user = find_user_by_id($me['id']);
            $me = $user;
        } else {
            $errors[] = "Profile update failed.";
        }
        $stmt->close();
    }
}

$posts = get_user_posts($user['id']);
include 'includes/header.php';
?>
<div class="profile-top card">
  <img class="avatar-large" src="<?php echo e($user['profile_pic']); ?>" alt="">
  <div class="profile-info">
    <h2><?php echo e($user['full_name']); ?></h2>
    <p class="muted">Joined <?php echo e(date('M j, Y', strtotime($user['created_at'] ?? 'now'))); ?></p>

    <?php if ($editing): ?>
      <?php if ($errors): foreach ($errors as $er): ?><div class="alert"><?php echo e($er); ?></div><?php endforeach; endif; ?>
      <?php if ($success): ?><div class="success"><?php echo e($success); ?></div><?php endif; ?>
      <form method="post" enctype="multipart/form-data" class="edit-profile">
        <label>Display name<input name="full_name" value="<?php echo e($me['full_name']); ?>"></label>
        <label>Change picture <input type="file" name="profile_pic" id="profile-input-2" accept="image/*"></label>
        <div id="profile-preview-2" class="img-preview"></div>
        <button type="submit">Save changes</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<div class="profile-posts">
  <h3><?php echo ($editing) ? 'Your posts' : e($user['full_name'] . "'s posts"); ?></h3>
  <?php if (empty($posts)): ?><p class="muted">No posts yet.</p><?php else: foreach ($posts as $p): ?>
    <div class="post card">
      <div class="post-header">
        <img src="<?php echo e($user['profile_pic']); ?>" class="avatar-small" alt="">
        <div>
          <a class="post-author" href="profile.php?user_id=<?php echo e($user['id']); ?>"><?php echo e($user['full_name']); ?></a>
          <div class="muted"><?php echo e(date('M j, Y H:i', strtotime($p['created_at']))); ?></div>
        </div>
      </div>
      <div class="post-body">
        <p><?php echo nl2br(e($p['content'])); ?></p>
        <?php if (!empty($p['image'])): ?><div class="post-image"><img src="<?php echo e($p['image']); ?>" alt=""></div><?php endif; ?>
      </div>
    </div>
  <?php endforeach; endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
