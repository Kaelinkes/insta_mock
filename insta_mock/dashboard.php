<?php
require_once 'includes/functions.php';
if (empty($_SESSION['user_id'])) {
    header('Location: index.php'); exit;
}
$me = find_user_by_id($_SESSION['user_id']);
$errors = []; $success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_post'])) {
    $content = trim($_POST['content'] ?? '');
    $imgPath = null;
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $saved = save_image_upload($_FILES['post_image']);
        if ($saved) $imgPath = $saved;
        else $errors[] = "Post image upload failed or invalid.";
    }
    if (!$content && !$imgPath) $errors[] = "Post cannot be empty.";
    if (empty($errors)) {
        if (create_post($me['id'], $content, $imgPath)) $success = "Post published.";
        else $errors[] = "Could not publish post.";
    }
}

$posts = get_feed();
include 'includes/header.php';
?>
<div class="panel">
  <div class="left">
    <div class="create-post card">
      <div class="who">
        <img src="<?php echo e($me['profile_pic']); ?>" class="avatar-small" alt="">
        <div><strong><?php echo e($me['full_name']); ?></strong></div>
      </div>

      <?php if ($errors): foreach ($errors as $er): ?>
        <div class="alert"><?php echo e($er); ?></div>
      <?php endforeach; endif; ?>
      <?php if ($success): ?><div class="success"><?php echo e($success); ?></div><?php endif; ?>

      <form method="post" enctype="multipart/form-data">
        <textarea name="content" placeholder="Share something..." rows="3"></textarea>
        <label class="file-label">Attach image <input type="file" name="post_image" accept="image/*" id="post-input"></label>
        <div id="post-preview" class="img-preview"></div>
        <button name="create_post" type="submit">Post</button>
      </form>
    </div>

    <div class="feed">
      <?php if (empty($posts)): ?>
        <p class="muted">No posts yet.</p>
      <?php else: foreach ($posts as $p): ?>
        <div class="post card">
          <div class="post-header">
            <img src="<?php echo e($p['profile_pic']); ?>" class="avatar-small" alt="">
            <div>
              <a class="post-author" href="profile.php?user_id=<?php echo e($p['user_id']); ?>"><?php echo e($p['full_name']); ?></a>
              <div class="muted"><?php echo e(date('M j, Y H:i', strtotime($p['created_at']))); ?></div>
            </div>
          </div>
          <div class="post-body">
            <p><?php echo nl2br(e($p['content'])); ?></p>
            <?php if (!empty($p['image'])): ?>
              <div class="post-image"><img src="<?php echo e($p['image']); ?>" alt=""></div>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </div>

  <aside class="right">
    <div class="profile-card card">
      <img class="avatar-large" src="<?php echo e($me['profile_pic']); ?>" alt="">
      <h3><?php echo e($me['full_name']); ?></h3>
      <p class="muted">Member since <?php echo e(date('M Y', strtotime($me['created_at'] ?? 'now'))); ?></p>
      <a class="btn" href="profile.php?user_id=<?php echo e($me['id']); ?>">View profile</a>
    </div>
  </aside>
</div>
<?php include 'includes/footer.php'; ?>
