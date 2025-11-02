<?php
require_once 'includes/functions.php';
if (empty($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$me = find_user_by_id($_SESSION['user_id']);
$errors = [];

$contact_id = intval($_GET['contact'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $to = intval($_POST['to']);
    $msg = trim($_POST['message'] ?? '');
    if (!$msg) $errors[] = "Message cannot be empty.";
    else {
        if (send_message($me['id'], $to, $msg)) {
            header('Location: message.php?contact=' . $to);
            exit;
        } else $errors[] = "Failed to send message.";
    }
}

$contacts = search_users(''); // all users (limit inside function)
if ($contact_id) $conversation = get_conversation($me['id'], $contact_id);
else $conversation = [];

include 'includes/header.php';
?>
<div class="messages-panel">
  <aside class="contacts card">
    <h4>Contacts</h4>
    <?php foreach ($contacts as $c): if ($c['id'] == $me['id']) continue; ?>
      <a class="contact <?php echo ($c['id']==$contact_id) ? 'active' : ''; ?>" href="message.php?contact=<?php echo $c['id']; ?>">
        <img src="<?php echo e($c['profile_pic']); ?>" class="avatar-small" alt="">
        <div><strong><?php echo e($c['full_name']); ?></strong><div class="muted"><?php echo e($c['email']); ?></div></div>
      </a>
    <?php endforeach; ?>
  </aside>

  <section class="conversation card">
    <?php if (!$contact_id): ?>
      <p class="muted">Select a contact to view conversation.</p>
    <?php else: $contact = find_user_by_id($contact_id); ?>
      <h3>Chat with <?php echo e($contact['full_name']); ?></h3>
      <div class="messages">
        <?php foreach ($conversation as $m): ?>
          <div class="message <?php echo ($m['sender_id']==$me['id']) ? 'sent' : 'received'; ?>">
            <div class="meta"><strong><?php echo e($m['sender_name']); ?></strong> <span class="muted"><?php echo e(date('M j, H:i', strtotime($m['created_at']))); ?></span></div>
            <div class="body"><?php echo nl2br(e($m['message'])); ?></div>
          </div>
        <?php endforeach; ?>
      </div>

      <?php if ($errors): foreach ($errors as $er): ?><div class="alert"><?php echo e($er); ?></div><?php endforeach; endif; ?>

      <form method="post" class="message-form">
        <input type="hidden" name="to" value="<?php echo $contact_id; ?>">
        <textarea name="message" rows="3" placeholder="Write a message..."></textarea>
        <button name="send_message" type="submit">Send</button>
      </form>
    <?php endif; ?>
  </section>
</div>
<?php include 'includes/footer.php'; ?>
