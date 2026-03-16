<?php
require_once __DIR__ . '/../adding/db.php';
require_once __DIR__ . '/../adding/functions.php';
session_start();
require_admin();

$action = $_GET['action'] ?? 'list';
$edit_id = (int)($_GET['id'] ?? 0);
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$players = $pdo->query("SELECT * FROM players ORDER BY total_wins DESC, total_games ASC")->fetchAll();
$edit_player = null;
if ($action === 'edit' && $edit_id) {
  $s = $pdo->prepare("SELECT * FROM players WHERE id = ?");
  $s->execute([$edit_id]);
  $edit_player = $s->fetch();
  if (!$edit_player) {
    redirect('players.php');
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Players — v4d Admin</title>
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
        <h2 class="admin-page-title">
          <?= $action === 'list' ? 'Players' : ($action === 'add' ? 'Add Player' : 'Edit Player')?>
        </h2>
        <?php if ($action === 'list'): ?>
        <a href="?action=add" class="btn btn-primary btn-sm">➕ Add Player</a>
        <?php
else: ?>
        <a href="players.php" class="btn btn-outline btn-sm">← Back</a>
        <?php
endif; ?>
      </div>

      <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type']?> flash-message">
        <?= sanitize($flash['msg'])?>
      </div>
      <?php
endif; ?>

      <?php if ($action === 'list'): ?>
      <!-- Players List -->
      <div class="admin-table-wrap glass-card">
        <?php if (empty($players)): ?>
        <div class="empty-state">
          <div class="empty-icon">👥</div>
          <p>No players yet. <a href="?action=add" class="text-primary">Add one!</a></p>
        </div>
        <?php
  else: ?>
        <table class="admin-table table-players">
          <thead>
            <tr>
              <th>Player</th>
              <th>Font</th>
              <th>Wins</th>
              <th>Games</th>
              <th>Win Rate</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($players as $p): ?>
            <tr>
              <td class="admin-player-cell">
                <?php if ($p['profile_pic']): ?>
                <img src="../../uploads_v4d/profiles/<?= sanitize($p['profile_pic'])?>" class="admin-avatar">
                <?php
      else: ?>
                <span class="admin-avatar-ph">🎮</span>
                <?php
      endif; ?>
                <span class="<?= font_class($p['font_style'])?>">
                  <?= sanitize($p['name'])?>
                </span>
              </td>
              <td><small>
                  <?= ucfirst($p['font_style'])?>
                </small></td>
              <td class="text-primary">
                <?= $p['total_wins']?>
              </td>
              <td class="text-muted">
                <?= $p['total_games']?>
              </td>
              <td>
                <?= win_rate($p['total_wins'], $p['total_games'])?>
              </td>
              <td class="admin-actions-cell">
                <a href="?action=edit&id=<?= $p['id']?>" class="btn btn-outline btn-sm">Edit</a>
                <button class="btn btn-danger btn-sm"
                  onclick="confirmDelete(<?= $p['id']?>, '<?= sanitize($p['name'])?>')">Delete</button>
              </td>
            </tr>
            <?php
    endforeach; ?>
          </tbody>
        </table>
        <?php
  endif; ?>
      </div>

      <?php
else: ?>
      <!-- Add / Edit Form -->
      <form class="admin-form" method="POST" action="../api/save_player.php" enctype="multipart/form-data">
        <?php if ($edit_player): ?>
        <input type="hidden" name="id" value="<?= $edit_player['id']?>">
        <?php
  endif; ?>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="name">Player Name *</label>
            <input type="text" id="name" name="name" class="form-control" required
              value="<?= sanitize($edit_player['name'] ?? '')?>" placeholder="e.g. V4D_SNIPER">
          </div>
          <div class="form-group">
            <label class="form-label" for="username">Username (Login) *</label>
            <input type="text" id="username" name="username" class="form-control" required
              value="<?= sanitize($edit_player['username'] ?? '')?>" placeholder="Leave blank for none">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="password">Password <?= !$edit_player ? '*' : '' ?></label>
            <input type="password" id="password" name="password" class="form-control" <?= !$edit_player ? 'required' : '' ?>
              placeholder="<?= $edit_player ? 'Leave blank to keep existing' : 'Enter password'?>">
          </div>
          <div class="form-group">
            <label class="form-label" for="font_style">Name Font Style *</label>
            <select id="font_style" name="font_style" class="form-control" onchange="updatePreview()">
              <?php
  $fonts = [
    'modern' => 'Modern Stylish', 
    'techy' => 'Techy', 
    'pixelated' => 'Pixelated', 
    'aesthetic' => 'Aesthetic',
    'bebas' => 'Bebas (Impact)',
    'cinzel' => 'Cinzel (Cinematic)',
    'marker' => 'Marker (Handwritten)',
    'russo' => 'Russo (Blocky)',
    'creepster' => 'Creepster (Horror)'
  ];
  $cur = $edit_player['font_style'] ?? 'modern';
  foreach ($fonts as $key => $label): ?>
              <option value="<?= $key?>" <?= $cur === $key ? 'selected' : '' ?>>
                <?= $label?>
              </option>
              <?php
  endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="bio">Bio / Description</label>
          <textarea id="bio" name="bio" class="form-control" rows="4"
            placeholder="Player bio, gaming history, achievements..."><?= sanitize($edit_player['bio'] ?? '')?></textarea>
        </div>

        <!-- Colors -->
        <div class="form-group">
          <label class="form-label">Card & Text Colors</label>
          <div class="color-row">
            <div class="color-group">
              <label for="card_color">Card BG</label>
              <input type="color" id="card_color" name="card_color"
                value="<?= $edit_player['card_color'] ?? '#1a1a1a'?>" oninput="updatePreview()">
            </div>
            <div class="color-group">
              <label for="text_color">Text Color</label>
              <input type="color" id="text_color" name="text_color"
                value="<?= $edit_player['text_color'] ?? '#ffffff'?>" oninput="updatePreview()">
            </div>
            <div class="color-group">
              <label for="border_color">Border / Glow</label>
              <input type="color" id="border_color" name="border_color"
                value="<?= $edit_player['border_color'] ?? '#f5a623'?>" oninput="updatePreview()">
            </div>
          </div>
        </div>

        <!-- Stats -->
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="total_wins">Total Wins</label>
            <input type="number" id="total_wins" name="total_wins" class="form-control" min="0"
              value="<?=(int)($edit_player['total_wins'] ?? 0)?>">
          </div>
          <div class="form-group">
            <label class="form-label" for="total_games">Total Games Played</label>
            <input type="number" id="total_games" name="total_games" class="form-control" min="0"
              value="<?=(int)($edit_player['total_games'] ?? 0)?>">
          </div>
        </div>

        <!-- Image uploads -->
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Profile Picture</label>
            <?php if (!empty($edit_player['profile_pic'])): ?>
            <img src="../../uploads_v4d/profiles/<?= sanitize($edit_player['profile_pic'])?>" class="upload-preview-img"
              id="profile-preview" alt="Current profile pic">
            <?php
  else: ?>
            <div class="upload-preview-ph" id="profile-preview">🎮</div>
            <?php
  endif; ?>
            <input type="file" name="profile_pic" id="profile_pic" class="form-control" accept="image/*"
              onchange="previewImg(this, 'profile-preview')">
            <small class="text-muted">JPG/PNG/WEBP, max 2MB</small>
          </div>
          <div class="form-group">
            <label class="form-label">Cover Image (wide banner)</label>
            <?php if (!empty($edit_player['cover_image'])): ?>
            <img src="../../uploads_v4d/covers/<?= sanitize($edit_player['cover_image'])?>" class="upload-preview-cover"
              id="cover-preview" alt="Current cover">
            <?php
  else: ?>
            <div class="upload-preview-cover-ph" id="cover-preview">No cover set</div>
            <?php
  endif; ?>
            <input type="file" name="cover_image" id="cover_image" class="form-control" accept="image/*"
              onchange="previewImg(this, 'cover-preview')">
            <small class="text-muted">JPG/PNG/WEBP, max 5MB</small>
          </div>
        </div>

        <!-- Live Preview -->
        <div class="form-group">
          <label class="form-label">Live Card Preview</label>
          <div class="player-card preview-card" id="card-preview"
            style="max-width:300px; background: <?= $edit_player['card_color'] ?? '#1a1a1a'?>; --border-color: <?= $edit_player['border_color'] ?? '#f5a623'?>; --text-color: <?= $edit_player['text_color'] ?? '#ffffff'?>">
            <div class="player-card-cover-placeholder"></div>
            <div class="player-card-body">
              <div class="player-card-avatar-wrapper">
                <div class="player-card-avatar-placeholder" id="preview-avatar">🎮</div>
              </div>
              <div class="player-card-name <?= font_class($edit_player['font_style'] ?? 'modern')?>" id="preview-name"
                style="color: <?= $edit_player['text_color'] ?? '#fff'?>">
                <?= sanitize($edit_player['name'] ?? 'Player Name')?>
              </div>
            </div>
          </div>
        </div>

        <div class="admin-form-actions">
          <button type="submit" class="btn btn-primary">
            <?= $edit_player ? '💾 Save Changes' : '➕ Add Player'?>
          </button>
          <a href="players.php" class="btn btn-outline">Cancel</a>
        </div>
      </form>
      <?php
endif; ?>
    </main>
  </div>

  <!-- Delete Modal -->
  <div class="modal-overlay" id="delete-modal" style="display:none;">
    <div class="modal-box">
      <h3>Delete Player</h3>
      <p id="delete-modal-msg">Are you sure?</p>
      <div class="modal-actions">
        <form method="POST" action="../api/delete_player.php">
          <input type="hidden" name="id" id="delete-player-id">
          <button type="submit" class="btn btn-danger">Yes, Delete</button>
        </form>
        <button class="btn btn-outline" onclick="closeModal()">Cancel</button>
      </div>
    </div>
  </div>

  <script src="../js/main.js"></script>
  <script src="../js/admin.js"></script>
  <script>
    function confirmDelete(id, name) {
      document.getElementById('delete-player-id').value = id;
      document.getElementById('delete-modal-msg').textContent = 'Delete "' + name + '"? This cannot be undone.';
      document.getElementById('delete-modal').style.display = 'flex';
    }
    function closeModal() {
      document.getElementById('delete-modal').style.display = 'none';
    }
  </script>
</body>

</html>