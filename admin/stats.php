<?php
require_once __DIR__ . '/../adding/db.php';
require_once __DIR__ . '/../adding/functions.php';
session_start();
require_admin();

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$players = $pdo->query("SELECT * FROM players ORDER BY total_wins DESC, total_games ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['update_pid'])) {
        // Single update
        $pid = (int)$_POST['update_pid'];
        $wins  = (int)($_POST['wins'][$pid]  ?? 0);
        $games = (int)($_POST['games'][$pid] ?? 0);
        
        $upd = $pdo->prepare("UPDATE players SET total_wins=?, total_games=? WHERE id=?");
        $upd->execute([$wins, $games, $pid]);
        $_SESSION['flash'] = ['type'=>'success','msg'=>"Player stats updated!"];
    } else {
        // Bulk update
        $ids = $_POST['pid'] ?? [];
        foreach ($ids as $pid) {
            $pid   = (int)$pid;
            $wins  = (int)($_POST['wins'][$pid]  ?? 0);
            $games = (int)($_POST['games'][$pid] ?? 0);
            if ($pid) {
                $upd = $pdo->prepare("UPDATE players SET total_wins=?, total_games=? WHERE id=?");
                $upd->execute([$wins, $games, $pid]);
            }
        }
        $_SESSION['flash'] = ['type'=>'success','msg'=>'All stats updated successfully!'];
    }
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

    <div class="alert alert-info">Edit wins and games played directly. Click <strong>Update</strong> by a specific player or <strong>Save All</strong> when done.</div>

    <?php if (empty($players)): ?>
      <div class="empty-state glass-card"><div class="empty-icon">👥</div><p>No players to update.</p></div>
    <?php else: ?>
    
    <div style="margin-bottom: 20px;">
      <input type="text" id="statsSearch" class="form-control" placeholder="Search player name..." onkeyup="filterStatsTable()" style="max-width: 400px; width: 100%;">
    </div>

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
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($players as $i => $p): ?>
            <tr>
              <td>
                <span class="rank-badge rank-<?= ($i+1) <= 3 ? ($i+1) : 'other' ?>">#<?= $i+1 ?></span>
                <input type="hidden" name="pid[]" value="<?= $p['id'] ?>">
              </td>
              <td class="admin-player-cell player-name-cell">
                <?php if ($p['profile_pic']): ?>
                  <img src="../../uploads/profiles/<?= sanitize($p['profile_pic']) ?>" class="admin-avatar">
                <?php else: ?>
                  <span class="admin-avatar-ph">🎮</span>
                <?php endif; ?>
                <span class="<?= font_class($p['font_style']) ?>"><?= sanitize($p['name']) ?></span>
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
              <td><button type="submit" name="update_pid" value="<?= $p['id'] ?>" class="btn btn-outline btn-sm" style="width: 100%;">Update</button></td>
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
<script>
function filterStatsTable() {
    let input = document.getElementById("statsSearch");
    let filter = input.value.toLowerCase();
    let rows = document.querySelectorAll(".stats-table tbody tr");
    
    rows.forEach(row => {
        let nameCell = row.querySelector(".player-name-cell span:last-child");
        if (nameCell) {
            let name = nameCell.innerText.toLowerCase();
            if (name.includes(filter)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        }
    });
}
</script>
</body>
</html>
