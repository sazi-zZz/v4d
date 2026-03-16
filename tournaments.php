<?php
require_once __DIR__ . '/adding/db.php';
require_once __DIR__ . '/adding/functions.php';

$page_title = 'Tournaments';

$tournaments = $pdo->query("SELECT * FROM tournaments ORDER BY created_at DESC")->fetchAll();

include __DIR__ . '/adding/header.php';
?>

<section class="page-header">
  <div class="container">
    <h1>🏆 Tournaments</h1>
    <p>All v4d clan tournaments and battle records</p>
  </div>
</section>

<main class="main-content">
  <div class="container">
    <?php if (empty($tournaments)): ?>
      <div class="empty-state glass-card">
        <div class="empty-icon">🏆</div>
        <p>No tournaments recorded yet. Stay tuned!</p>
      </div>
    <?php else: ?>
    <div class="grid-3">
      <?php foreach ($tournaments as $t): ?>
      <a href="tournament.php?id=<?= $t['id'] ?>" class="tournament-card" data-anim>
        <?php if ($t['banner']): ?>
          <img src="../uploads_v4d/banners/<?= sanitize($t['banner']) ?>" alt="<?= sanitize($t['name']) ?>" class="tournament-card-banner" loading="lazy">
        <?php else: ?>
          <div class="tournament-card-banner-placeholder">🏆</div>
        <?php endif; ?>
        <div class="tournament-card-body">
          <div class="tournament-card-title"><?= sanitize($t['name']) ?></div>
          <?php if ($t['description']): ?>
            <div class="tournament-card-desc"><?= strip_tags($t['description']) ?></div>
          <?php endif; ?>
          <div class="tournament-card-meta">
            <?= date('M j, Y', strtotime($t['created_at'])) ?>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</main>

<?php include __DIR__ . '/adding/footer.php'; ?>
