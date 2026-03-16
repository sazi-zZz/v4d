<?php
require_once __DIR__ . '/../adding/db.php';
require_once __DIR__ . '/../adding/functions.php';
session_start();
require_admin();

$id = (int)($_POST['id'] ?? 0);
if (!$id) {
    $_SESSION['flash'] = ['type'=>'error','msg'=>'Invalid player ID.'];
    redirect('../admin/players.php');
}

// Remove uploaded files
$p = $pdo->prepare("SELECT profile_pic, cover_image FROM players WHERE id = ?");
$p->execute([$id]);
$player = $p->fetch();
if ($player) {
    if ($player['profile_pic'] && file_exists(__DIR__ . '/../../uploads_v4d/profiles/' . $player['profile_pic'])) {
        @unlink(__DIR__ . '/../../uploads_v4d/profiles/' . $player['profile_pic']);
    }
    if ($player['cover_image'] && file_exists(__DIR__ . '/../../uploads_v4d/covers/' . $player['cover_image'])) {
        @unlink(__DIR__ . '/../../uploads_v4d/covers/' . $player['cover_image']);
    }
}

$stmt = $pdo->prepare("DELETE FROM players WHERE id = ?");
$stmt->execute([$id]);

$_SESSION['flash'] = ['type'=>'success','msg'=>'Player deleted.'];
redirect('../admin/players.php');
