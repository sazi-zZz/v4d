<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
session_start();
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/admin/players.php');

$id         = (int)($_POST['id'] ?? 0);
$name       = trim($_POST['name'] ?? '');
$username   = trim($_POST['username'] ?? '') ?: null;
$password   = trim($_POST['password'] ?? '');
$bio        = trim($_POST['bio'] ?? '');
$font_style = trim($_POST['font_style'] ?? 'modern');
$card_color = trim($_POST['card_color'] ?? '#1a1a1a');
$text_color = trim($_POST['text_color'] ?? '#ffffff');
$border_color = trim($_POST['border_color'] ?? '#f5a623');
$total_wins   = max(0, (int)($_POST['total_wins']  ?? 0));
$total_games  = max(0, (int)($_POST['total_games'] ?? 0));

if (!$name) {
    $_SESSION['flash'] = ['type'=>'error','msg'=>'Player name is required.'];
    redirect('/admin/players.php?action=' . ($id ? 'edit&id=' . $id : 'add'));
}

if (!$username) {
    $_SESSION['flash'] = ['type'=>'error','msg'=>'Username is required.'];
    redirect('/admin/players.php?action=' . ($id ? 'edit&id=' . $id : 'add'));
}

if (!$password) {
    $_SESSION['flash'] = ['type'=>'error','msg'=>'Password is required.'];
    redirect('/admin/players.php?action=' . ($id ? 'edit&id=' . $id : 'add'));
}

if ($username) {
    $u_check = $pdo->prepare("SELECT id FROM players WHERE username = ? AND id != ?");
    $u_check->execute([$username, $id]);
    if ($u_check->fetch()) {
        $_SESSION['flash'] = ['type'=>'error','msg'=>'Username is already taken.'];
        redirect('/admin/players.php?action=' . ($id ? 'edit&id=' . $id : 'add'));
    }
}

// Allowed fonts
$valid_fonts = ['techy','pixelated','modern','aesthetic'];
if (!in_array($font_style, $valid_fonts)) $font_style = 'modern';

// Handle uploads
$profile_pic  = null;
$cover_image  = null;

if (!empty($_FILES['profile_pic']['name'])) {
    $profile_pic = handle_upload('profile_pic', __DIR__ . '/../uploads/profiles/', ['jpg','jpeg','png','webp','gif'], 2 * 1024 * 1024);
}
if (!empty($_FILES['cover_image']['name'])) {
    $cover_image = handle_upload('cover_image', __DIR__ . '/../uploads/covers/', ['jpg','jpeg','png','webp','gif'], 5 * 1024 * 1024);
}

if ($id) {
    // Edit — fetch existing to preserve images if not replaced
    $existing = $pdo->prepare("SELECT profile_pic, cover_image FROM players WHERE id = ?");
    $existing->execute([$id]);
    $ex = $existing->fetch();

    if (!$profile_pic && $ex) $profile_pic = $ex['profile_pic'];
    if (!$cover_image && $ex) $cover_image = $ex['cover_image'];

    if ($password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            "UPDATE players SET name=?, username=?, password_hash=?, bio=?, font_style=?, card_color=?, text_color=?, border_color=?,
             profile_pic=?, cover_image=?, total_wins=?, total_games=? WHERE id=?"
        );
        $stmt->execute([$name, $username, $hash, $bio, $font_style, $card_color, $text_color, $border_color,
                        $profile_pic, $cover_image, $total_wins, $total_games, $id]);
    } else {
        $stmt = $pdo->prepare(
            "UPDATE players SET name=?, username=?, bio=?, font_style=?, card_color=?, text_color=?, border_color=?,
             profile_pic=?, cover_image=?, total_wins=?, total_games=? WHERE id=?"
        );
        $stmt->execute([$name, $username, $bio, $font_style, $card_color, $text_color, $border_color,
                        $profile_pic, $cover_image, $total_wins, $total_games, $id]);
    }
    $_SESSION['flash'] = ['type'=>'success','msg'=>"Player \"$name\" updated successfully!"];
} else {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare(
        "INSERT INTO players (name, username, password_hash, bio, font_style, card_color, text_color, border_color,
         profile_pic, cover_image, total_wins, total_games) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)"
    );
    $stmt->execute([$name, $username, $hash, $bio, $font_style, $card_color, $text_color, $border_color,
                    $profile_pic, $cover_image, $total_wins, $total_games]);
    $_SESSION['flash'] = ['type'=>'success','msg'=>"Player \"$name\" added successfully!"];
}

redirect('/admin/players.php');
