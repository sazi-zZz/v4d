<?php
require_once __DIR__ . '/../adding/db.php';
require_once __DIR__ . '/../adding/functions.php';
session_start();
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('../admin/tournaments.php');

$id          = (int)($_POST['id'] ?? 0);
$name        = trim($_POST['name'] ?? '');
$description = $_POST['description'] ?? ''; // trusted admin HTML from Quill

if (!$name) {
    $_SESSION['flash'] = ['type'=>'error','msg'=>'Tournament name is required.'];
    redirect('../admin/tournaments.php?action=' . ($id ? 'edit&id='.$id : 'add'));
}

// Handle banner upload
$banner = null;
if (!empty($_FILES['banner']['name'])) {
    $banner = handle_upload('banner', __DIR__ . '/../../uploads/banners/', ['jpg','jpeg','png','webp','gif'], 5 * 1024 * 1024);
}

if ($id) {
    $ex = $pdo->prepare("SELECT banner FROM tournaments WHERE id = ?");
    $ex->execute([$id]);
    $ex = $ex->fetch();
    if (!$banner && $ex) $banner = $ex['banner'];

    $stmt = $pdo->prepare("UPDATE tournaments SET name=?, description=?, banner=? WHERE id=?");
    $stmt->execute([$name, $description, $banner, $id]);
    $_SESSION['flash'] = ['type'=>'success','msg'=>"Tournament \"$name\" updated!"];
} else {
    $stmt = $pdo->prepare("INSERT INTO tournaments (name, description, banner) VALUES (?,?,?)");
    $stmt->execute([$name, $description, $banner]);
    $id = (int)$pdo->lastInsertId();
    $_SESSION['flash'] = ['type'=>'success','msg'=>"Tournament \"$name\" created!"];
}

// Save participant stats
if (!empty($_POST['p_wins'])) {
    foreach ($_POST['p_wins'] as $player_id => $wins) {
        $player_id = (int)$player_id;
        $wins      = max(0, (int)$wins);
        $games     = max(0, (int)($_POST['p_games'][$player_id] ?? 0));

        if ($wins > 0 || $games > 0) {
            $upsert = $pdo->prepare(
                "INSERT INTO tournament_stats (tournament_id, player_id, wins, games) VALUES (?,?,?,?)
                 ON DUPLICATE KEY UPDATE wins=VALUES(wins), games=VALUES(games)"
            );
            $upsert->execute([$id, $player_id, $wins, $games]);
        } else {
            // Remove if zeroed out
            $del = $pdo->prepare("DELETE FROM tournament_stats WHERE tournament_id=? AND player_id=?");
            $del->execute([$id, $player_id]);
        }
    }
}

redirect('../admin/tournaments.php');
