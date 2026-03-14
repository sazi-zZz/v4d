<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
session_start();
require_admin();

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$players = $pdo->query("SELECT * FROM players ORDER BY total_wins DESC, total_games ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Bulk update
    $ids = $_POST['pid'] ?? [];
    foreach ($ids as $pid) {
        $pid   = (int)$pid;
        $wins  = (int)($_POST['wins'][$pid]  ?? 0);
        $games = (int)($_POST['games'][$pid] ?? 0);
        $name  = trim($_POST['name'][$pid]   ?? '');
        if ($pid && $name) {
            $upd = $pdo->prepare("UPDATE players SET total_wins=?, total_games=?, name=? WHERE id=?");
            $upd->execute([$wins, $games, $name, $pid]);
        }
    }
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Stats updated successfully!'];
    redirect('stats.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Stats — v4d Admin</title>
  <link rel="icon" type="image/png" href="../css/img/v4d.png">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="bg-grid">
<div class="glow-orb glow-orb-1" aria-hidden="true"></div>

<div class="admin-layout">
  <?php include __DIR__ . '/partials/sidebar.php'; ?>

  <main class="admin-main">
    <div class="admin-topbar">
      <h2 class="admin-page-title">✏️ Update Leaderboard Stats</h2>
    </div>

    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?> flash-message"><?= sanitize($flash['msg']) ?></div>
    <?php endif; ?>

    <div class="alert alert-info">Edit player names, wins, and games played directly. Click <strong>Save All</strong> when done.</div>

    <?php if (empty($players)): ?>
      <div class="empty-state glass-card"><div class="empty-icon">👥</div><p>No players to update.</p></div>
    <?php else: ?>
    <form method="POST" action="">
      <div class="admin-table-wrap glass-card">
        <table class="admin-table stats-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Player Name</th>
              <th>Wins</th>
              <th>Games Played</th>
              <th>Win Rate</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($players as $i => $p): ?>
            <tr>
              <td>
                <span class="rank-badge rank-<?= ($i+1) <= 3 ? ($i+1) : 'other' ?>">#<?= $i+1 ?></span>
                <input type="hidden" name="pid[]" value="<?= $p['id'] ?>">
              </td>
              <td>
                <input type="text" name="name[<?= $p['id'] ?>]" value="<?= sanitize($p['name']) ?>"
                       class="form-control <?= font_class($p['font_style']) ?>" required>
              </td>
              <td>
                <input type="number" name="wins[<?= $p['id'] ?>]" value="<?= $p['total_wins'] ?>"
                       class="form-control form-control-inline" min="0">
              </td>
              <td>
                <input type="number" name="games[<?= $p['id'] ?>]" value="<?= $p['total_games'] ?>"
                       class="form-control form-control-inline" min="0">
              </td>
              <td class="text-primary"><?= win_rate($p['total_wins'], $p['total_games']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="admin-form-actions mt-2">
        <button type="submit" class="btn btn-primary">💾 Save All Stats</button>
      </div>
    </form>
    <?php endif; ?>
  </main>
</div>

<script src="../js/main.js"></script>
</body>
</html>
