<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Home';
$extra_css = ['home.css'];

// Fetch top 3 for hero stats
$top3 = $pdo->query("SELECT * FROM players ORDER BY total_wins DESC, total_games ASC LIMIT 3")->fetchAll();
$total_players = $pdo->query("SELECT COUNT(*) FROM players")->fetchColumn();
$total_tournaments = $pdo->query("SELECT COUNT(*) FROM tournaments")->fetchColumn();

include __DIR__ . '/includes/header.php';
?>

<!-- ============================================================
     HERO SECTION
     ============================================================ -->
<section class="hero" aria-label="Hero">
  <div class="hero-bg">
    <img src="/v4d/css/img/v4d.jpeg" alt="" class="hero-bg-img" aria-hidden="true">
    <div class="hero-overlay"></div>
  </div>
  <div class="hero-content container">
    <img src="/v4d/css/img/v4d.png" alt="v4d Esports Logo" class="hero-logo" data-anim>
    <h1 class="hero-title" data-anim>Vanguard 4 Dominance</h1>
    <p class="hero-sub" data-anim>Unleash the vanguards, siege dominance</p>
    <div class="hero-actions" data-anim>
      <a href="/v4d/leaderboard.php" class="btn btn-primary">⚡ Leaderboard</a>
      <a href="/v4d/tournaments.php" class="btn btn-outline">🏆 Tournaments</a>
    </div>
    <div class="hero-stats" data-anim>
      <div class="hero-stat">
        <span class="hero-stat-value">
          <?= $total_players?>
        </span>
        <span class="hero-stat-label">Players</span>
      </div>
      <div class="hero-stat-divider"></div>
      <div class="hero-stat">
        <span class="hero-stat-value">
          <?= $total_tournaments?>
        </span>
        <span class="hero-stat-label">Tournaments</span>
      </div>
      <?php
$total_wins = $pdo->query("SELECT COALESCE(SUM(total_wins),0) FROM players")->fetchColumn();
?>
      <div class="hero-stat-divider"></div>
      <div class="hero-stat">
        <span class="hero-stat-value">
          <?= $total_wins?>
        </span>
        <span class="hero-stat-label">Total Wins</span>
      </div>
    </div>
  </div>
  <div class="hero-scroll-hint" aria-hidden="true">
    <span></span>
  </div>
</section>

<!-- ============================================================
     TOP PLAYERS SECTION
     ============================================================ -->
<?php if (!empty($top3)): ?>
<section class="section" aria-label="Top Players">
  <div class="container">
    <h2 class="section-title">🏅 Top Players</h2>
    <div class="section-divider"></div>
    <div class="grid-3">
      <?php foreach ($top3 as $i => $player): ?>
      <a href="/v4d/player.php?id=<?= $player['id']?>" class="player-card"
        style="background: <?= sanitize($player['card_color'])?>; --border-color: <?= sanitize($player['border_color'])?>; --text-color: <?= sanitize($player['text_color'])?>;"
        data-anim>
        <?php if ($player['cover_image']): ?>
        <img src="/v4d/uploads/covers/<?= sanitize($player['cover_image'])?>" alt="" class="player-card-cover"
          loading="lazy">
        <?php
    else: ?>
        <div class="player-card-cover-placeholder"></div>
        <?php
    endif; ?>
        <div class="player-card-body">
          <div class="d-flex align-center gap-2">
            <div class="player-card-avatar-wrapper">
              <?php if ($player['profile_pic']): ?>
              <img src="/v4d/uploads/profiles/<?= sanitize($player['profile_pic'])?>"
                alt="<?= sanitize($player['name'])?>" class="player-card-avatar">
              <?php
    else: ?>
              <div class="player-card-avatar-placeholder">🎮</div>
              <?php
    endif; ?>
            </div>
            <span class="rank-badge rank-<?=($i < 3) ? ($i + 1) : 'other'?>">#
              <?= $i + 1?>
            </span>
          </div>
          <div class="player-card-name <?= font_class($player['font_style'])?>">
            <?= sanitize($player['name'])?>
          </div>
          <?php if ($player['bio']): ?>
          <div class="player-card-bio">
            <?= sanitize($player['bio'])?>
          </div>
          <?php
    endif; ?>
          <div class="player-card-stats">
            <div class="stat-item">
              <div class="stat-value">
                <?= $player['total_wins']?>
              </div>
              <div class="stat-label">Wins</div>
            </div>
            <div class="stat-item">
              <div class="stat-value">
                <?= $player['total_games']?>
              </div>
              <div class="stat-label">Games</div>
            </div>
            <div class="stat-item">
              <div class="stat-value">
                <?= win_rate($player['total_wins'], $player['total_games'])?>
              </div>
              <div class="stat-label">Win Rate</div>
            </div>
          </div>
        </div>
      </a>
      <?php
  endforeach; ?>
    </div>
    <div class="text-center mt-3">
      <a href="/v4d/leaderboard.php" class="btn btn-outline">View Full Leaderboard →</a>
    </div>
  </div>
</section>
<?php
endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>