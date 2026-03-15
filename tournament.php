<?php
require_once __DIR__ . '/adding/db.php';
require_once __DIR__ . '/adding/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { redirect(base_url('tournaments.php')); }

$tournament = $pdo->prepare("SELECT * FROM tournaments WHERE id = ?");
$tournament->execute([$id]);
$tournament = $tournament->fetch();

if (!$tournament) { redirect(base_url('tournaments.php')); }

// Fetch participants for this tournament
$participants = $pdo->prepare(
    "SELECT p.*, ts.wins, ts.games,
            CASE WHEN ts.games > 0 THEN ROUND((ts.wins/ts.games)*100,1) ELSE 0 END AS wr
     FROM tournament_stats ts
     JOIN players p ON ts.player_id = p.id
     WHERE ts.tournament_id = ?
     ORDER BY ts.wins DESC, ts.games ASC"
);
$participants->execute([$id]);
$participants = $participants->fetchAll();

$page_title   = $tournament['name'];
$extra_css    = ['tournament.css'];

include __DIR__ . '/adding/header.php';
?>

<!-- Tournament Banner -->
<div class="tournament-hero">
  <?php if ($tournament['banner']): ?>
    <img src="uploads/banners/<?= sanitize($tournament['banner']) ?>" alt="<?= sanitize($tournament['name']) ?>" class="tournament-hero-img">
  <?php else: ?>
    <div class="tournament-hero-placeholder"></div>
  <?php endif; ?>
  <div class="tournament-hero-overlay">
    <div class="container">
      <a href="tournaments.php" class="back-link">← Back to Tournaments</a>
      <h1 class="tournament-hero-title"><?= sanitize($tournament['name']) ?></h1>
      <span class="tournament-hero-date"><?= date('F j, Y', strtotime($tournament['created_at'])) ?></span>
    </div>
  </div>
</div>

<main class="main-content">
  <div class="container">
    <div class="tournament-layout">

      <!-- Description -->
      <?php if ($tournament['description']): ?>
      <div class="tournament-description glass-card" data-anim>
        <h2 class="section-title">📋 Tournament Details</h2>
        <div class="section-divider"></div>
        <div class="rich-content">
          <?= $tournament['description'] /* trusted admin HTML */ ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- Participant Stats -->
      <div class="tournament-stats-section" data-anim>
        <h2 class="section-title">🏅 Participant Stats</h2>
        <div class="section-divider"></div>

        <?php if (empty($participants)): ?>
          <div class="empty-state glass-card">
            <div class="empty-icon">👤</div>
            <p>No participant stats recorded for this tournament.</p>
          </div>
        <?php else: ?>
        <div class="t-stats-grid">
          <?php foreach ($participants as $rank => $p): $r = $rank + 1; ?>
          <a href="player.php?id=<?= $p['id'] ?>" class="t-stat-card glass-card">
            <span class="rank-badge rank-<?= $r <= 3 ? $r : 'other' ?>">#<?= $r ?></span>
            <div class="t-stat-player">
              <?php if ($p['profile_pic']): ?>
                <img src="uploads/profiles/<?= sanitize($p['profile_pic']) ?>" alt="" class="t-stat-avatar" style="border-color:<?= sanitize($p['border_color']) ?>">
              <?php else: ?>
                <span class="t-stat-avatar-ph">🎮</span>
              <?php endif; ?>
              <div>
                <div class="t-stat-name <?= font_class($p['font_style']) ?>"><?= sanitize($p['name']) ?></div>
                <div class="t-stat-wr"><?= $p['wr'] ?>% Win Rate</div>
              </div>
            </div>
            <div class="t-stat-nums">
              <div><span class="stat-value"><?= $p['wins'] ?></span><br><small class="stat-label">Wins</small></div>
              <div><span class="stat-value text-muted"><?= $p['games'] ?></span><br><small class="stat-label">Games</small></div>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</main>

<?php include __DIR__ . '/adding/footer.php'; ?>
