<?php
require_once __DIR__ . '/../adding/db.php';
require_once __DIR__ . '/../adding/functions.php';
session_start();
require_admin();

$id = (int)($_POST['id'] ?? 0);
if (!$id) {
    $_SESSION['flash'] = ['type'=>'error','msg'=>'Invalid tournament ID.'];
    redirect('../admin/tournaments.php');
}

$ex = $pdo->prepare("SELECT banner FROM tournaments WHERE id = ?");
$ex->execute([$id]);
$t = $ex->fetch();
if ($t && $t['banner'] && file_exists(__DIR__ . '/../../uploads/banners/' . $t['banner'])) {
    @unlink(__DIR__ . '/../../uploads/banners/' . $t['banner']);
}

$stmt = $pdo->prepare("DELETE FROM tournaments WHERE id = ?");
$stmt->execute([$id]);

$_SESSION['flash'] = ['type'=>'success','msg'=>'Tournament deleted.'];
redirect('../admin/tournaments.php');
