/* =======================================
   admin.js - v4d Admin JavaScript
   ======================================= */

/**
 * Live card preview: updates each time a field changes.
 */
function updatePreview() {
  const card   = document.getElementById('card-preview');
  const name   = document.getElementById('name');
  const nameEl = document.getElementById('preview-name');
  const font   = document.getElementById('font_style');

  if (!card) return;

  if (name && nameEl) {
    nameEl.textContent = name.value || 'Player Name';
  }

  const cardColor   = document.getElementById('card_color')?.value   || '#1a1a1a';
  const textColor   = document.getElementById('text_color')?.value   || '#ffffff';
  const borderColor = document.getElementById('border_color')?.value || '#f5a623';

  card.style.background   = cardColor;
  card.style.setProperty('--border-color', borderColor);
  card.style.setProperty('--text-color',   textColor);

  if (nameEl && textColor) {
    nameEl.style.color = textColor;
  }

  // Font class
  const fontMap = {
    techy:     'font-techy',
    pixelated: 'font-pixelated',
    modern:    'font-modern',
    aesthetic: 'font-aesthetic',
    bebas:     'font-bebas',
    cinzel:    'font-cinzel',
    marker:    'font-marker',
    russo:     'font-russo',
    creepster: 'font-creepster',
  };

  if (nameEl && font) {
    nameEl.className = nameEl.className
      .replace(/font-(techy|pixelated|modern|aesthetic|bebas|cinzel|marker|russo|creepster)/g, '')
      .trimEnd();
    const cls = fontMap[font.value];
    if (cls) nameEl.classList.add(cls, 'player-card-name');
  }
}

// Attach live update to name field
const nameInput = document.getElementById('name');
if (nameInput) {
  nameInput.addEventListener('input', updatePreview);
}

/**
 * Image upload preview
 */
function previewImg(input, targetId) {
  const target = document.getElementById(targetId);
  if (!target || !input.files || !input.files[0]) return;

  const file = input.files[0];
  if (!file.type.startsWith('image/')) {
    alert('Please select an image file (JPG, PNG, WEBP, GIF).');
    input.value = '';
    return;
  }

  const reader = new FileReader();
  reader.onload = (e) => {
    // If target is an <img>, update src; if div, create/replace img
    if (target.tagName === 'IMG') {
      target.src = e.target.result;
    } else {
      // Replace placeholder with img
      const img = document.createElement('img');
      img.src   = e.target.result;
      img.id    = targetId;

      // Decide class based on original class
      if (target.classList.contains('upload-preview-cover-ph')) {
        img.className = 'upload-preview-cover';
      } else {
        img.className = 'upload-preview-img';
      }

      target.parentNode.replaceChild(img, target);
    }
  };
  reader.readAsDataURL(file);
}

// Init preview on page load if in edit mode
document.addEventListener('DOMContentLoaded', updatePreview);
