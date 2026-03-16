<?php
require_once __DIR__ . '/adding/db.php';
require_once __DIR__ . '/adding/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { redirect(base_url('leaderboard.php')); }

$player = $pdo->prepare("SELECT * FROM players WHERE id = ?");
$player->execute([$id]);
$player = $player->fetch();

if (!$player) { redirect(base_url('leaderboard.php')); }

// Global rank
$rank_result = $pdo->prepare(
    "SELECT COUNT(*) + 1 FROM players
     WHERE total_wins > ? OR (total_wins = ? AND total_games < ?)"
);
$rank_result->execute([$player['total_wins'], $player['total_wins'], $player['total_games']]);
$rank = $rank_result->fetchColumn();

// Tournaments participated in
$t_list = $pdo->prepare(
    "SELECT t.id, t.name, t.banner, ts.wins, ts.games
     FROM tournament_stats ts
     JOIN tournaments t ON ts.tournament_id = t.id
     WHERE ts.player_id = ?
     ORDER BY t.created_at DESC"
);
$t_list->execute([$id]);
$t_list = $t_list->fetchAll();

$page_title = $player['name'];
$extra_css  = ['player.css'];

include __DIR__ . '/adding/header.php';
?>

<!-- Player Profile Header -->
<div class="profile-hero" style="background: <?= sanitize($player['card_color']) ?>">
  <!-- Cover -->
  <div class="profile-cover">
    <?php if ($player['cover_image']): ?>
      <img src="../uploads/covers/<?= sanitize($player['cover_image']) ?>" alt="" class="profile-cover-img">
    <?php else: ?>
      <div class="profile-cover-placeholder"></div>
    <?php endif; ?>
    <div class="profile-cover-overlay"></div>
  </div>

  <div class="container profile-info-bar">
    <div class="profile-avatar-area">
      <?php if ($player['profile_pic']): ?>
        <img src="../uploads/profiles/<?= sanitize($player['profile_pic']) ?>"
             alt="<?= sanitize($player['name']) ?>"
             class="profile-avatar"
             style="border-color: <?= sanitize($player['border_color']) ?>; box-shadow: 0 0 24px <?= sanitize($player['border_color']) ?>66">
      <?php else: ?>
        <div class="profile-avatar-ph"
             style="border-color: <?= sanitize($player['border_color']) ?>">🎮</div>
      <?php endif; ?>
    </div>
    <div class="profile-meta">
      <h1 class="profile-name <?= font_class($player['font_style']) ?>" style="color: <?= sanitize($player['text_color']) ?>">
        <?= sanitize($player['name']) ?>
      </h1>
      <div class="profile-rank-badge-wrap">
        <span class="rank-badge rank-<?= $rank <= 3 ? $rank : 'other' ?>">Rank #<?= $rank ?></span>
        <span class="profile-clan-tag">v4d Esports</span>
      </div>
    </div>
    <div style="display: flex; gap: 8px;">
      <?php if (is_player() && current_player_id() === $player['id']): ?>
        <a href="profile_edit.php" class="btn btn-primary btn-sm">Edit Profile ✏️</a>
      <?php endif; ?>
      <a href="leaderboard.php" class="btn btn-outline btn-sm">← Back</a>
    </div>
  </div>
</div>

<main class="main-content">
  <div class="container">
    <div class="profile-layout">

      <!-- Sidebar -->
      <aside class="profile-sidebar">
        <!-- Stats Card -->
        <div class="glass-card profile-stats-card" data-anim>
          <h3>📊 Statistics</h3>
          <div class="profile-stat-row">
            <span>Total Wins</span>
            <strong class="text-primary"><?= $player['total_wins'] ?></strong>
          </div>
          <div class="profile-stat-row">
            <span>Total Games</span>
            <strong><?= $player['total_games'] ?></strong>
          </div>
          <div class="profile-stat-row">
            <span>Win Rate</span>
            <strong class="text-primary"><?= win_rate($player['total_wins'], $player['total_games']) ?></strong>
          </div>
          <div class="profile-stat-row">
            <span>Clan Rank</span>
            <strong>#<?= $rank ?></strong>
          </div>
          <div class="win-rate-display">
            <div class="win-rate-bar-wrap">
              <div class="win-rate-bar" style="width:<?= $player['total_games'] > 0 ? min(round(($player['total_wins']/$player['total_games'])*100,1),100) : 0 ?>%"></div>
            </div>
            <small class="text-muted"><?= win_rate($player['total_wins'], $player['total_games']) ?> win rate</small>
          </div>
        </div>

        <!-- Style card -->
        <div class="glass-card profile-style-card" data-anim>
          <h3>🎨 Profile Style</h3>
          <div class="profile-stat-row">
            <span>Font Style</span>
            <strong class="<?= font_class($player['font_style']) ?>"><?= ucfirst($player['font_style']) ?></strong>
          </div>
          <div class="color-preview-row">
            <div class="color-dot" style="background:<?= sanitize($player['card_color']) ?>" title="Card Color"></div>
            <div class="color-dot" style="background:<?= sanitize($player['text_color']) ?>" title="Text Color"></div>
            <div class="color-dot" style="background:<?= sanitize($player['border_color']) ?>" title="Border Color"></div>
          </div>
        </div>
      </aside>

      <!-- Main Column -->
      <div class="profile-main-col">

        <!-- Bio -->
        <?php if ($player['bio']): ?>
        <div class="glass-card profile-bio-card" data-anim>
          <h3>👤 About</h3>
          <p class="profile-bio-text"><?= nl2br(sanitize($player['bio'])) ?></p>
        </div>
        <?php endif; ?>

        <!-- Tournaments -->
        <?php if (!empty($t_list)): ?>
        <div class="profile-tournaments" data-anim>
          <h3 class="section-title">🏆 Tournament History</h3>
          <div class="section-divider"></div>
          <div class="profile-t-list">
            <?php foreach ($t_list as $t): ?>
            <a href="tournament.php?id=<?= $t['id'] ?>" class="profile-t-row glass-card">
              <?php if ($t['banner']): ?>
                <img src="../uploads/banners/<?= sanitize($t['banner']) ?>" alt="" class="profile-t-thumb">
              <?php else: ?>
                <div class="profile-t-thumb-ph">🏆</div>
              <?php endif; ?>
              <div class="profile-t-info">
                <div class="profile-t-name"><?= sanitize($t['name']) ?></div>
                <div class="profile-t-stats">
                  <span class="text-primary"><?= $t['wins'] ?> wins</span>
                  <span class="text-muted">/ <?= $t['games'] ?> games</span>
                </div>
              </div>
              <span class="profile-t-arrow">→</span>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/adding/footer.php'; ?>
