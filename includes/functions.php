<?php

function sanitize(string $str): string {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

function font_class(string $style): string {
    $map = [
        'techy'     => 'font-techy',
        'pixelated' => 'font-pixelated',
        'modern'    => 'font-modern',
        'aesthetic' => 'font-aesthetic',
    ];
    return $map[$style] ?? 'font-modern';
}

function win_rate(int $wins, int $games): string {
    if ($games === 0) return '0%';
    return round(($wins / $games) * 100, 1) . '%';
}

/**
 * Handle a file upload. Returns the saved filename (relative to $dest_dir) or null.
 * @param string $field_name   $_FILES key
 * @param string $dest_dir     Absolute path to destination directory (with trailing slash)
 * @param array  $allowed_ext  Allowed extensions e.g. ['jpg','jpeg','png','webp']
 * @param int    $max_bytes    Max file size in bytes
 */
function handle_upload(string $field_name, string $dest_dir, array $allowed_ext = ['jpg','jpeg','png','webp','gif'], int $max_bytes = 5 * 1024 * 1024): ?string {
    if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $file = $_FILES[$field_name];
    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_ext, true)) {
        return null;
    }
    if ($file['size'] > $max_bytes) {
        return null;
    }
    // Validate MIME
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $allowed_mimes = ['image/jpeg','image/png','image/gif','image/webp'];
    if (!in_array($mime, $allowed_mimes, true)) {
        return null;
    }

    $filename = uniqid('', true) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $dest_dir . $filename)) {
        return null;
    }
    return $filename;
}

function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}

function is_admin(): bool {
    return !empty($_SESSION['admin_id']);
}

function require_admin(): void {
    if (!is_admin()) {
        redirect('/v4d/admin/login.php');
    }
}

function is_player(): bool {
    return !empty($_SESSION['player_id']);
}

function current_player_id(): ?int {
    return $_SESSION['player_id'] ?? null;
}

function require_player(): void {
    if (!is_player()) {
        redirect('/v4d/auth/player_login.php');
    }
}
