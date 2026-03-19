<?php
require_once __DIR__ . '/../adding/db.php';
require_once __DIR__ . '/../adding/functions.php';
session_start();
require_admin();

$action  = $_GET['action'] ?? 'list';
$edit_id = (int)($_GET['id'] ?? 0);
$flash   = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$tournaments = $pdo->query("SELECT * FROM tournaments ORDER BY created_at DESC")->fetchAll();
$players     = $pdo->query("SELECT id, name FROM players ORDER BY name ASC")->fetchAll();

$edit_t  = null;
$t_stats = [];
if ($action === 'edit' && $edit_id) {
    $s = $pdo->prepare("SELECT * FROM tournaments WHERE id = ?");
    $s->execute([$edit_id]);
    $edit_t = $s->fetch();
    if (!$edit_t) redirect('tournaments.php');

    $ss = $pdo->prepare("SELECT player_id, wins, games FROM tournament_stats WHERE tournament_id = ?");
    $ss->execute([$edit_id]);
    $t_stats = [];
    foreach ($ss->fetchAll() as $row) {
        $t_stats[$row['player_id']] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tournaments — v4d Admin</title>
  <link rel="icon" type="image/png" href="../css/img/v4d.png">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/admin.css">
  <!-- Quill rich text editor -->
  <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.7/quill.snow.css">
</head>
<body class="bg-grid">
<div class="glow-orb glow-orb-1" aria-hidden="true"></div>

<div class="admin-layout">
  <?php include __DIR__ . '/partials/sidebar.php'; ?>

  <main class="admin-main">
    <div class="admin-topbar">
      <h2 class="admin-page-title">
        <?= $action === 'list' ? 'Tournaments' : ($action === 'add' ? 'Add Tournament' : 'Edit Tournament') ?>
      </h2>
      <?php if ($action === 'list'): ?>
        <a href="?action=add" class="btn btn-primary btn-sm">➕ Add Tournament</a>
      <?php else: ?>
        <a href="tournaments.php" class="btn btn-outline btn-sm">← Back</a>
      <?php endif; ?>
    </div>

    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?> flash-message"><?= sanitize($flash['msg']) ?></div>
    <?php endif; ?>

    <?php if ($action === 'list'): ?>
    <!-- Tournament List -->
    <div class="admin-table-wrap glass-card">
      <?php if (empty($tournaments)): ?>
        <div class="empty-state"><div class="empty-icon">🏆</div><p>No tournaments yet. <a href="?action=add" class="text-primary">Add one!</a></p></div>
      <?php else: ?>
      <table class="admin-table table-tournaments">
        <thead>
          <tr>
            <th>Banner</th>
            <th>Tournament Name</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($tournaments as $t): ?>
          <tr>
            <td>
              <?php if ($t['banner']): ?>
                <img src="<?= upload_url("banners/" . $t['banner']) ?>" class="admin-banner-thumb">
              <?php else: ?>
                <span class="admin-banner-ph">🏆</span>
              <?php endif; ?>
            </td>
            <td><strong><?= sanitize($t['name']) ?></strong></td>
            <td class="text-muted"><?= date('M j, Y', strtotime($t['created_at'])) ?></td>
            <td class="admin-actions-cell">
              <a href="?action=edit&id=<?= $t['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
              <button class="btn btn-danger btn-sm" onclick="confirmDeleteT(<?= $t['id'] ?>, '<?= sanitize($t['name']) ?>')">Delete</button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>

    <?php else: ?>
    <!-- Add / Edit Form -->
    <form class="admin-form" method="POST" action="../api/save_tournament.php" enctype="multipart/form-data" id="tournament-form">
      <?php if ($edit_t): ?>
        <input type="hidden" name="id" value="<?= $edit_t['id'] ?>">
      <?php endif; ?>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="t_name">Tournament Name *</label>
          <input type="text" id="t_name" name="name" class="form-control" required
                 value="<?= sanitize($edit_t['name'] ?? '') ?>" placeholder="e.g. v4d Winter Championship">
        </div>
        <div class="form-group">
          <label class="form-label">Banner Image</label>
          <?php if (!empty($edit_t['banner'])): ?>
            <img src="<?= upload_url("banners/" . $edit_t['banner']) ?>" class="upload-preview-cover" id="banner-preview">
          <?php else: ?>
            <div class="upload-preview-cover-ph" id="banner-preview">No banner set</div>
          <?php endif; ?>
          <input type="file" name="banner" class="form-control" accept="image/*"
                 onchange="previewImg(this, 'banner-preview')">
          <small class="text-muted">Recommended: 1200×400px, JPG/PNG/WEBP, max 5MB</small>
        </div>
      </div>

      <!-- Rich Text Description -->
      <div class="form-group">
        <label class="form-label">Tournament Description</label>
        <div id="quill-editor" style="height:300px; background:var(--color-surface2); border-radius:10px; color:#fff;"></div>
        <textarea name="description" id="description-hidden" style="display:none;"><?= $edit_t['description'] ?? '' ?></textarea>
      </div>

      <!-- Participant Stats -->
      <div class="form-group">
        <label class="form-label">Participant Stats (optional)</label>
        <div class="t-stats-edit-wrap">
          <table class="admin-table" id="stats-table">
            <thead>
              <tr>
                <th>Player</th>
                <th>Wins</th>
                <th>Games</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($players as $p): ?>
              <?php $ps = $t_stats[$p['id']] ?? ['wins'=>0,'games'=>0]; ?>
              <tr>
                <td><?= sanitize($p['name']) ?></td>
                <td><input type="number" name="p_wins[<?= $p['id'] ?>]" class="form-control form-control-inline" value="<?= (int)$ps['wins'] ?>" min="0"></td>
                <td><input type="number" name="p_games[<?= $p['id'] ?>]" class="form-control form-control-inline" value="<?= (int)$ps['games'] ?>" min="0"></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="admin-form-actions">
        <button type="submit" class="btn btn-primary">
          <?= $edit_t ? '💾 Save Changes' : '➕ Add Tournament' ?>
        </button>
        <a href="tournaments.php" class="btn btn-outline">Cancel</a>
      </div>
    </form>
    <?php endif; ?>

  </main>
</div>

<!-- Delete Modal -->
<div class="modal-overlay" id="delete-modal" style="display:none;">
  <div class="modal-box">
    <h3>Delete Tournament</h3>
    <p id="delete-modal-msg">Are you sure?</p>
    <div class="modal-actions">
      <form method="POST" action="../api/delete_tournament.php">
        <input type="hidden" name="id" id="delete-t-id">
        <button type="submit" class="btn btn-danger">Yes, Delete</button>
      </form>
      <button class="btn btn-outline" onclick="document.getElementById('delete-modal').style.display='none'">Cancel</button>
    </div>
  </div>
</div>

<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script src="../js/main.js"></script>
<script src="../js/admin.js"></script>
<script>
// Init Quill
const quill = new Quill('#quill-editor', {
  theme: 'snow',
  placeholder: 'Describe the tournament — rules, map, prizes, match schedule...',
  modules: {
    toolbar: [
      [{ header: [1, 2, 3, false] }],
      ['bold', 'italic', 'underline', 'strike'],
      [{ color: [] }, { background: [] }],
      [{ list: 'ordered' }, { list: 'bullet' }],
      [{ align: [] }],
      ['link', 'image'],
      ['clean']
    ]
  }
});

// Set existing content
const existing = document.getElementById('description-hidden').value;
if (existing) quill.root.innerHTML = existing;

// On submit, copy Quill HTML to hidden textarea
const form = document.getElementById('tournament-form');
if (form) {
  form.addEventListener('submit', () => {
    document.getElementById('description-hidden').value = quill.root.innerHTML;
  });
}

function confirmDeleteT(id, name) {
  document.getElementById('delete-t-id').value = id;
  document.getElementById('delete-modal-msg').textContent = 'Delete "' + name + '"? This cannot be undone.';
  document.getElementById('delete-modal').style.display = 'flex';
}
</script>
</body>
</html>
