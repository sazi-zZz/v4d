<?php
require_once __DIR__ . '../includes/db.php';
require_once __DIR__ . '../includes/functions.php';
session_start();
require_admin();

$page_title = 'Admin Dashboard';

$total_players = $pdo->query("SELECT COUNT(*) FROM players")->fetchColumn();
$total_tournaments = $pdo->query("SELECT COUNT(*) FROM tournaments")->fetchColumn();
$total_wins = $pdo->query("SELECT COALESCE(SUM(total_wins),0) FROM players")->fetchColumn();
$top_player = $pdo->query("SELECT name FROM players ORDER BY total_wins DESC, total_games ASC LIMIT 1")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard — v4d Esports</title>
  <link rel="icon" type="image/png" href="../css/img/v4d.png">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/admin.css">
</head>

<body class="bg-grid">
  <div class="glow-orb glow-orb-1" aria-hidden="true"></div>

  <div class="admin-layout">
    <!-- Sidebar -->
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <!-- Main -->
    <main class="admin-main">
      <div class="admin-topbar">
        <h2 class="admin-page-title">Dashboard</h2>
        <span class="admin-greeting">Welcome, <strong>
            <?= sanitize($_SESSION['admin_user'])?>
          </strong></span>
      </div>

      <!-- Stats grid -->
      <div class="dash-stats">
        <div class="dash-stat-card">
          <div class="dash-stat-icon">👥</div>
          <div class="dash-stat-value">
            <?= $total_players?>
          </div>
          <div class="dash-stat-label">Players</div>
        </div>
        <div class="dash-stat-card">
          <div class="dash-stat-icon">🏆</div>
          <div class="dash-stat-value">
            <?= $total_tournaments?>
          </div>
          <div class="dash-stat-label">Tournaments</div>
        </div>
        <div class="dash-stat-card">
          <div class="dash-stat-icon">⚡</div>
          <div class="dash-stat-value">
            <?= $total_wins?>
          </div>
          <div class="dash-stat-label">Total Wins</div>
        </div>
        <div class="dash-stat-card">
          <div class="dash-stat-icon">👑</div>
          <div class="dash-stat-value" style="font-size:1rem">
            <?= $top_player ? sanitize($top_player) : '—'?>
          </div>
          <div class="dash-stat-label">Top Player</div>
        </div>
      </div>

      <!-- Quick links -->
      <div class="dash-quick-links">
        <a href="players.php?action=add" class="dash-quick-card">
          <span>➕</span> Add Player
        </a>
        <a href="tournaments.php?action=add" class="dash-quick-card">
          <span>➕</span> Add Tournament
        </a>
        <a href="stats.php" class="dash-quick-card">
          <span>✏️</span> Update Stats
        </a>
        <a href="../index.php" target="_blank" class="dash-quick-card">
          <span>🌐</span> View Site
        </a>
      </div>

    </main>
  </div>

  <script src="../js/main.js"></script>
</body>

</html>