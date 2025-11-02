<?php
// includes/functions.php
require_once __DIR__ . '/../config.php';

function find_user_by_email($email) {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

function find_user_by_id($id) {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

function register_user($name, $email, $password, $profile_pic = 'uploads/default_profile.png') {
    global $mysqli;
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("INSERT INTO users (full_name, email, password, profile_pic) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $name, $email, $hash, $profile_pic);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function login_user($email, $password) {
    $user = find_user_by_email($email);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        return true;
    }
    return false;
}

function save_image_upload($file) {
    // Accept image uploads and return relative path or null
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return null;
    $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
    if (!in_array($file['type'], $allowed)) return null;
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $name = uniqid('img_') . '.' . $ext;
    $dir = __DIR__ . '/../uploads';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $target = $dir . '/' . $name;
    if (move_uploaded_file($file['tmp_name'], $target)) {
        return 'uploads/' . $name;
    }
    return null;
}

function create_post($user_id, $content, $image = null) {
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO posts (user_id, content, image) VALUES (?, ?, ?)");
    $stmt->bind_param('iss', $user_id, $content, $image);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function get_feed($limit = 100) {
    global $mysqli;
    $sql = "SELECT p.*, u.full_name, u.profile_pic FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function get_user_posts($user_id) {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function search_users($term) {
    global $mysqli;
    $like = '%' . $term . '%';
    $stmt = $mysqli->prepare("SELECT id, full_name, email, profile_pic FROM users WHERE full_name LIKE ? OR email LIKE ? LIMIT 50");
    $stmt->bind_param('ss', $like, $like);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function send_message($sender_id, $receiver_id, $message) {
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param('iis', $sender_id, $receiver_id, $message);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function get_conversation($a, $b) {
    global $mysqli;
    $stmt = $mysqli->prepare("
        SELECT m.*, u.full_name AS sender_name
        FROM messages m
        JOIN users u ON u.id = m.sender_id
        WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
        ORDER BY m.created_at ASC
    ");
    $stmt->bind_param('iiii', $a, $b, $b, $a);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function generate_token() {
    return bin2hex(random_bytes(16));
}
?>
