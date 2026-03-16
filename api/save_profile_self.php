<?php
require_once __DIR__ . '/../adding/db.php';
require_once __DIR__ . '/../adding/functions.php';
session_start();

header('Content-Type: application/json');

if (!is_player()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$player_id = current_player_id();

// Fetch current to keep old images if not replaced
$stmt = $pdo->prepare("SELECT profile_pic, cover_image FROM players WHERE id = ?");
$stmt->execute([$player_id]);
$current = $stmt->fetch();

if (!$current) {
    echo json_encode(['success' => false, 'message' => 'Player not found']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$username = trim($_POST['username'] ?? '') ?: null; // convert empty string to null so UNIQUE constraint works
$password = trim($_POST['password'] ?? '');
$bio = trim($_POST['bio'] ?? '');
$font_style = $_POST['font_style'] ?? 'modern';
$card_color = $_POST['card_color'] ?? '#1a1a1a';
$text_color = $_POST['text_color'] ?? '#ffffff';
$border_color = $_POST['border_color'] ?? '#f5a623';

if (!$name) {
    echo json_encode(['success' => false, 'message' => 'Name is required.']);
    exit;
}

// Check username uniqueness if changing
if ($username) {
    $u_check = $pdo->prepare("SELECT id FROM players WHERE username = ? AND id != ?");
    $u_check->execute([$username, $player_id]);
    if ($u_check->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Username is already taken.']);
        exit;
    }
}

$profile_pic = $current['profile_pic'];
$cover_image = $current['cover_image'];

$p_upload = handle_upload('profile_pic', __DIR__ . '/../../uploads/profiles/');
if ($p_upload) $profile_pic = $p_upload;

$c_upload = handle_upload('cover_image', __DIR__ . '/../../uploads/covers/');
if ($c_upload) $cover_image = $c_upload;

// Only update password if provided
if ($password) {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $update = $pdo->prepare("UPDATE players SET 
        name=?, username=?, password_hash=?, bio=?, font_style=?, card_color=?, text_color=?, border_color=?, profile_pic=?, cover_image=? 
        WHERE id=?");
    $success = $update->execute([
        $name, $username, $password_hash, $bio, $font_style, $card_color, $text_color, $border_color, $profile_pic, $cover_image, $player_id
    ]);
} else {
    $update = $pdo->prepare("UPDATE players SET 
        name=?, username=?, bio=?, font_style=?, card_color=?, text_color=?, border_color=?, profile_pic=?, cover_image=? 
        WHERE id=?");
    $success = $update->execute([
        $name, $username, $bio, $font_style, $card_color, $text_color, $border_color, $profile_pic, $cover_image, $player_id
    ]);
}

if ($success) {
    $_SESSION['player_name'] = $name; // update session name
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
}
