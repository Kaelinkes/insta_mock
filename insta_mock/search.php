<?php
require_once 'includes/functions.php';
if (empty($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$q = trim($_GET['q'] ?? '');
$results = [];
if ($q !== '') $results = search_users($q);
include 'includes/header.php';
?>
<div class="search-results card">
  <h3>Search results for "<?php echo e($q); ?>"</h3>
  <?php if (empty($results)): ?><p class="muted">No users found.</p>
  <?php else: foreach ($results as $r): ?>
    <div class="search-item">
      <img src="<?php echo e($r['profile_pic']); ?>" class="avatar-small" alt="">
      <div><a href="profile.php?user_id=<?php echo e($r['id']); ?>"><strong><?php echo e($r['full_name']); ?></strong></a><div class="muted"><?php echo e($r['email']); ?></div></div>
    </div>
  <?php endforeach; endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
