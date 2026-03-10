<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
session_start();

require_player();
$player_id = current_player_id();

$stmt = $pdo->prepare("SELECT * FROM players WHERE id = ?");
$stmt->execute([$player_id]);
$player = $stmt->fetch();

if (!$player) {
    redirect('/v4d/auth/player_logout.php');
}

$page_title = 'Edit Profile';
$extra_css  = ['profile_edit.css'];

include __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 40px;">
  <div class="edit-form-card">
    <h1 style="text-align:center; font-family: var(--font-techy); color: var(--color-primary); margin-bottom: 24px;">Edit Profile</h1>
    
    <div id="alert-box" class="alert" style="display:none;"></div>

    <form id="editProfileForm" enctype="multipart/form-data">
      
      <!-- Basics -->
      <div class="edit-section">
        <h3>👤 Basic Info</h3>
        <div class="form-row">
          <div class="form-col">
            <label class="form-label">Player Name</label>
            <input type="text" name="name" class="form-control" value="<?= sanitize($player['name']) ?>" required>
          </div>
          <div class="form-col">
            <label class="form-label">Username (Login)</label>
            <input type="text" name="username" class="form-control" value="<?= sanitize($player['username'] ?? '') ?>">
            <small class="text-muted">Used for login. Leave empty if you don't want a password.</small>
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label">New Password</label>
          <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
        </div>

        <div class="form-group">
          <label class="form-label">Bio / About</label>
          <textarea name="bio" class="form-control" rows="3"><?= sanitize($player['bio']) ?></textarea>
        </div>
      </div>

      <!-- Images -->
      <div class="edit-section">
        <h3>🖼️ Photos</h3>
        <div class="form-row">
          <div class="form-col">
            <label class="form-label">Profile Photo</label>
            <div class="file-upload-wrap">
              <input type="file" name="profile_pic" accept="image/*" class="form-control">
            </div>
            <?php if ($player['profile_pic']): ?>
            <div class="preview-box">
              <img src="/v4d/uploads/profiles/<?= sanitize($player['profile_pic']) ?>" alt="Current Profile">
            </div>
            <?php endif; ?>
          </div>
          
          <div class="form-col">
            <label class="form-label">Cover Photo</label>
            <div class="file-upload-wrap">
              <input type="file" name="cover_image" accept="image/*" class="form-control">
            </div>
            <?php if ($player['cover_image']): ?>
            <div class="preview-box">
              <img src="/v4d/uploads/covers/<?= sanitize($player['cover_image']) ?>" alt="Current Cover">
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Colors & Typography -->
      <div class="edit-section">
        <h3>🎨 Style & Colors</h3>
        <div class="form-row">
          <div class="form-col">
            <label class="form-label">Card Background Color</label>
            <div class="color-picker-wrap">
              <input type="color" name="card_color" value="<?= sanitize($player['card_color']) ?>">
              <span class="text-muted">Main profile theme area</span>
            </div>
          </div>
          <div class="form-col">
            <label class="form-label">Border Focus Color</label>
            <div class="color-picker-wrap">
              <input type="color" name="border_color" value="<?= sanitize($player['border_color']) ?>">
              <span class="text-muted">Avatar ring & highlights</span>
            </div>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-col">
            <label class="form-label">Name Text Color</label>
            <div class="color-picker-wrap">
              <input type="color" name="text_color" value="<?= sanitize($player['text_color']) ?>" id="name_color_input">
            </div>
          </div>
          <div class="form-col">
            <label class="form-label">Name Font Style</label>
            <select name="font_style" class="form-control" id="font_style_input">
              <option value="modern" <?= $player['font_style'] === 'modern' ? 'selected' : '' ?>>Modern (Default)</option>
              <option value="techy" <?= $player['font_style'] === 'techy' ? 'selected' : '' ?>>Techy / E-Sports</option>
              <option value="pixelated" <?= $player['font_style'] === 'pixelated' ? 'selected' : '' ?>>Pixelated / Retro</option>
              <option value="aesthetic" <?= $player['font_style'] === 'aesthetic' ? 'selected' : '' ?>>Aesthetic / Script</option>
            </select>
          </div>
        </div>
        
        <div class="font-preview" id="font_preview_box" style="color: <?= sanitize($player['text_color']) ?>">
          <span id="font_preview_text" class="<?= font_class($player['font_style']) ?>"><?= sanitize($player['name']) ?></span>
        </div>
      </div>

      <div style="text-align:right;">
        <a href="/v4d/player.php?id=<?= $player['id'] ?>" class="btn btn-outline" style="margin-right: 12px;">Cancel</a>
        <button type="submit" class="btn btn-primary" id="saveBtn">Save Changes</button>
      </div>

    </form>
  </div>
</div>

<?php ob_start(); ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('editProfileForm');
  const alertBox = document.getElementById('alert-box');
  const saveBtn = document.getElementById('saveBtn');

  // Font preview logic
  const nameInput = form.querySelector('input[name="name"]');
  const fontStyleInput = document.getElementById('font_style_input');
  const nameColorInput = document.getElementById('name_color_input');
  const previewBox = document.getElementById('font_preview_box');
  const previewText = document.getElementById('font_preview_text');

  const updatePreview = () => {
    previewText.textContent = nameInput.value || 'Player Name';
    previewBox.style.color = nameColorInput.value;
    
    // Remove old font class and add new
    previewText.className = '';
    const map = {
        'techy': 'font-techy',
        'pixelated': 'font-pixelated',
        'modern': 'font-modern',
        'aesthetic': 'font-aesthetic'
    };
    previewText.classList.add(map[fontStyleInput.value] || 'font-modern');
  };

  nameInput.addEventListener('input', updatePreview);
  fontStyleInput.addEventListener('change', updatePreview);
  nameColorInput.addEventListener('input', updatePreview);

  // Submit handling
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    saveBtn.disabled = true;
    saveBtn.textContent = 'Saving...';
    alertBox.style.display = 'none';

    try {
      const fd = new FormData(form);
      const res = await fetch('/v4d/api/save_profile_self.php', {
        method: 'POST',
        body: fd
      });
      const data = await res.json();
      
      alertBox.className = data.success ? 'alert alert-success' : 'alert alert-error';
      alertBox.textContent = data.message || 'Error occurred';
      alertBox.style.display = 'block';

      if (data.success) {
        setTimeout(() => {
          window.location.href = '/v4d/player.php?id=<?= $player['id'] ?>';
        }, 1000);
      }
    } catch(err) {
      alertBox.className = 'alert alert-error';
      alertBox.textContent = 'Network error saving profile.';
      alertBox.style.display = 'block';
    } finally {
      saveBtn.disabled = false;
      saveBtn.textContent = 'Save Changes';
    }
  });
});
</script>
<?php 
$footer_scripts = ob_get_clean();
include __DIR__ . '/includes/footer.php'; 
?>
