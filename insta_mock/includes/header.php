<?php
// includes/header.php
require_once __DIR__ . '/functions.php';
$current_user = null;
if (!empty($_SESSION['user_id'])) {
    $current_user = find_user_by_id($_SESSION['user_id']);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>InstaMock</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="topbar">
  <div class="wrap">
    <div class="brand"><a href="dashboard.php">📸 InstaMock</a></div>
    <form class="search" action="search.php" method="get">
      <input name="q" id="live-search" type="search" placeholder="Search users..." autocomplete="off">
      <div id="live-results" class="live-results"></div>
    </form>
    <nav class="nav">
      <?php if ($current_user): ?>
        <a href="dashboard.php">Home</a>
        <a href="profile.php?user_id=<?php echo e($current_user['id']); ?>">Profile</a>
        <a href="message.php">Messages</a>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="index.php">Login</a>
        <a href="register.php">Register</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main class="container">
