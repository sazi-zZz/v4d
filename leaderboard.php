<?php
require_once __DIR__ . '/adding/db.php';
require_once __DIR__ . '/adding/functions.php';

$page_title = 'Leaderboard';
$extra_css   = ['leaderboard.css'];

// Leaderboard: most wins first, tie-breaker fewer games
$players = $pdo->query(
    "SELECT *, CASE WHEN total_games > 0 THEN ROUND((total_wins/total_games)*100,1) ELSE 0 END AS win_rate
     FROM players
     ORDER BY total_wins DESC, total_games ASC"
)->fetchAll();

include __DIR__ . '/adding/header.php';
?>

<section class="page-header">
  <div class="container">
    <h1>⚡ Leaderboard</h1>
    <p>Rankings by wins — fewer games breaks ties</p>
  </div>
</section>

<main class="main-content">
  <div class="container">

    <?php if (empty($players)): ?>
      <div class="empty-state glass-card">
        <div class="empty-icon">🎮</div>
        <p>No players yet. Check back soon!</p>
      </div>
    <?php else: ?>

    <!-- Top 3 Podium -->
    <?php
      $p1 = $players[0] ?? null;
      $p2 = $players[1] ?? null;
      $p3 = $players[2] ?? null;
    ?>
    <?php if ($p1): ?>
    <div class="podium-wrapper" aria-label="Top 3 Podium">
      <!-- 2nd place -->
      <?php if ($p2): ?>
      <div class="podium-spot podium-2nd" data-anim>
        <div class="podium-avatar-wrap">
          <?php if ($p2['profile_pic']): ?>
            <img src="<?= upload_url("profiles/" . $p2['profile_pic']) ?>" alt="<?= sanitize($p2['name']) ?>" class="podium-avatar" style="border-color: <?= sanitize($p2['border_color']) ?>">
          <?php else: ?>
            <div class="podium-avatar-ph" style="border-color: <?= sanitize($p2['border_color']) ?>">🎮</div>
          <?php endif; ?>
          <span class="podium-medal silver">🥈</span>
        </div>
        <a href="player.php?id=<?= $p2['id'] ?>" class="podium-name <?= font_class($p2['font_style']) ?>"><?= sanitize($p2['name']) ?></a>
        <div class="podium-wins"><?= $p2['total_wins'] ?> <small>wins</small></div>
        <div class="podium-block podium-block-2"></div>
      </div>
      <?php endif; ?>

      <!-- 1st place -->
      <div class="podium-spot podium-1st" data-anim>
        <div class="podium-crown">👑</div>
        <div class="podium-avatar-wrap">
          <?php if ($p1['profile_pic']): ?>
            <img src="<?= upload_url("profiles/" . $p1['profile_pic']) ?>" alt="<?= sanitize($p1['name']) ?>" class="podium-avatar podium-avatar-lg" style="border-color: <?= sanitize($p1['border_color']) ?>">
          <?php else: ?>
            <div class="podium-avatar-ph podium-avatar-lg" style="border-color: <?= sanitize($p1['border_color']) ?>">🎮</div>
          <?php endif; ?>
          <span class="podium-medal gold">🥇</span>
        </div>
        <a href="player.php?id=<?= $p1['id'] ?>" class="podium-name <?= font_class($p1['font_style']) ?>"><?= sanitize($p1['name']) ?></a>
        <div class="podium-wins"><?= $p1['total_wins'] ?> <small>wins</small></div>
        <div class="podium-block podium-block-1"></div>
      </div>

      <!-- 3rd place -->
      <?php if ($p3): ?>
      <div class="podium-spot podium-3rd" data-anim>
        <div class="podium-avatar-wrap">
          <?php if ($p3['profile_pic']): ?>
            <img src="<?= upload_url("profiles/" . $p3['profile_pic']) ?>" alt="<?= sanitize($p3['name']) ?>" class="podium-avatar" style="border-color: <?= sanitize($p3['border_color']) ?>">
          <?php else: ?>
            <div class="podium-avatar-ph" style="border-color: <?= sanitize($p3['border_color']) ?>">🎮</div>
          <?php endif; ?>
          <span class="podium-medal bronze">🥉</span>
        </div>
        <a href="player.php?id=<?= $p3['id'] ?>" class="podium-name <?= font_class($p3['font_style']) ?>"><?= sanitize($p3['name']) ?></a>
        <div class="podium-wins"><?= $p3['total_wins'] ?> <small>wins</small></div>
        <div class="podium-block podium-block-3"></div>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Search bar -->
    <div class="lb-search-wrap" data-anim>
      <div class="lb-search-box">
        <svg class="lb-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <input type="text" id="lb-search" class="lb-search-input" placeholder="Search player…" autocomplete="off" spellcheck="false">
        <button class="lb-search-clear" id="lb-search-clear" aria-label="Clear search" style="display:none;">✕</button>
      </div>
      <p class="lb-no-results" id="lb-no-results" style="display:none;">No players match "<span id="lb-no-results-term"></span>"</p>
    </div>

    <!-- Full Rankings Table (desktop) -->

    <div class="lb-table-wrap glass-card mt-4 lb-desktop" data-anim>
      <table class="lb-table" aria-label="Full leaderboard">
        <colgroup>
          <col style="width:52px">
          <col>
          <col style="width:50px">
          <col style="width:50px">
          <col style="width:56px">
          <col style="width:56px">
          <col style="width:110px">
        </colgroup>
        <thead>
          <tr>
            <th>Rank</th>
            <th>Player</th>
            <th>Duo</th>
            <th>Trio</th>
            <th>Total</th>
            <th>Games</th>
            <th>Win Rate</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($players as $rank => $p): $r = $rank + 1; ?>
          <tr class="lb-row <?= $r <= 3 ? 'lb-row-top' : '' ?>" onclick="window.location='player.php?id=<?= $p['id'] ?>'" style="cursor:pointer;">
            <td>
              <span class="rank-badge rank-<?= $r <= 3 ? $r : 'other' ?>">#<?= $r ?></span>
            </td>
            <td class="lb-player-cell">
              <?php if ($p['profile_pic']): ?>
                <img src="<?= upload_url("profiles/" . $p['profile_pic']) ?>" alt="" class="lb-avatar">
              <?php else: ?>
                <span class="lb-avatar-ph">🎮</span>
              <?php endif; ?>
              <span class="lb-player-name <?= font_class($p['font_style']) ?>" style="color: <?= sanitize($p['text_color']) ?>">
                <?= sanitize($p['name']) ?>
              </span>
            </td>
            <td class="lb-wins text-muted" style="font-size: 0.9em;"><?= $p['duo_wins'] ?></td>
            <td class="lb-wins text-muted" style="font-size: 0.9em;"><?= $p['trio_wins'] ?></td>
            <td class="lb-wins"><?= $p['total_wins'] ?></td>
            <td class="lb-games text-muted"><?= $p['total_games'] ?></td>
            <td>
              <div class="win-rate-bar-wrap">
                <div><div class="win-rate-bar" style="width: <?= min($p['win_rate'], 100) ?>%"></div></div>
                <span><?= $p['win_rate'] ?>%</span>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Mobile card list (phones only) -->
    <div class="lb-mobile-list mt-4 lb-mobile">
      <?php foreach ($players as $rank => $p): $r = $rank + 1; ?>
      <a href="player.php?id=<?= $p['id'] ?>" class="lb-mobile-card <?= $r <= 3 ? 'lb-mobile-card-top' : '' ?>">

        <!-- Left: rank + avatar -->
        <div class="lb-mc-left">
          <span class="rank-badge rank-<?= $r <= 3 ? $r : 'other' ?>">#<?= $r ?></span>
          <?php if ($p['profile_pic']): ?>
            <img src="<?= upload_url("profiles/" . $p['profile_pic']) ?>" alt="" class="lb-mc-avatar" style="border-color:<?= sanitize($p['border_color']) ?>">
          <?php else: ?>
            <div class="lb-mc-avatar lb-mc-avatar-ph" style="border-color:<?= sanitize($p['border_color']) ?>">🎮</div>
          <?php endif; ?>
        </div>

        <!-- Right: name + stats -->
        <div class="lb-mc-right">
          <div class="lb-mc-name <?= font_class($p['font_style']) ?>" style="color:<?= sanitize($p['text_color']) ?>">
            <?= sanitize($p['name']) ?>
          </div>
          <div class="lb-mc-stats">
            <span class="lb-mc-stat"><span class="lb-mc-stat-val"><?= $p['total_wins'] ?></span><span class="lb-mc-stat-lbl">Total</span></span>
            <span class="lb-mc-sep">·</span>
            <span class="lb-mc-stat"><span class="lb-mc-stat-val"><?= $p['duo_wins'] ?></span><span class="lb-mc-stat-lbl">Duo</span></span>
            <span class="lb-mc-sep">·</span>
            <span class="lb-mc-stat"><span class="lb-mc-stat-val"><?= $p['trio_wins'] ?></span><span class="lb-mc-stat-lbl">Trio</span></span>
            <span class="lb-mc-sep">·</span>
            <span class="lb-mc-stat"><span class="lb-mc-stat-val"><?= $p['total_games'] ?></span><span class="lb-mc-stat-lbl">Games</span></span>
            <span class="lb-mc-sep">·</span>
            <span class="lb-mc-stat">
              <span class="lb-mc-stat-val" style="color:var(--color-primary)"><?= $p['win_rate'] ?>%</span>
              <span class="lb-mc-stat-lbl">Win Rate</span>
            </span>
          </div>
          <div class="lb-mc-bar">
            <div class="lb-mc-bar-fill" style="width:<?= min($p['win_rate'],100) ?>%"></div>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

    <?php endif; ?>

  </div>
</main>

<?php include __DIR__ . '/adding/footer.php'; ?>

<script>
(function () {
  const input     = document.getElementById('lb-search');
  const clearBtn  = document.getElementById('lb-search-clear');
  const noResults = document.getElementById('lb-no-results');
  const noTerm    = document.getElementById('lb-no-results-term');

  // Desktop table rows — each has a data-name attribute we'll add below via PHP echo
  const tableRows  = document.querySelectorAll('.lb-table tbody tr');
  const mobileCards = document.querySelectorAll('.lb-mobile-card');

  function filter(query) {
    const q = query.trim().toLowerCase();
    let visibleCount = 0;

    tableRows.forEach(row => {
      const name = row.dataset.name || row.querySelector('.lb-player-name')?.textContent.toLowerCase() || '';
      const match = !q || name.includes(q);
      row.classList.toggle('lb-row-hidden', !match);
      if (match) visibleCount++;
    });

    mobileCards.forEach(card => {
      const name = card.dataset.name || card.querySelector('.lb-mc-name')?.textContent.toLowerCase() || '';
      const match = !q || name.includes(q);
      card.classList.toggle('lb-card-hidden', !match);
    });

    // No-results message (based on visible table rows)
    noResults.style.display = (q && visibleCount === 0) ? 'block' : 'none';
    if (noTerm) noTerm.textContent = query.trim();

    // Clear button
    clearBtn.style.display = q ? 'inline-block' : 'none';
  }

  input.addEventListener('input', () => filter(input.value));

  clearBtn.addEventListener('click', () => {
    input.value = '';
    filter('');
    input.focus();
  });
})();
</script>


