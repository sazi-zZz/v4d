<?php
require_once __DIR__ . '/../adding/db.php';
require_once __DIR__ . '/../adding/functions.php';
session_start();

// Standard header for AJAX
header('Content-Type: application/json');

if (!is_admin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['image'])) {
    echo json_encode(['error' => 'No image uploaded']);
    exit;
}

// 1. Upload the image using the handle_upload function (from functions.php)
// Destination: /uploads/tournaments/
$uploaded_filename = handle_upload('image', __DIR__ . '/../uploads/tournaments/', ['jpg','jpeg','png','webp','gif'], 5 * 1024 * 1024);

if (!$uploaded_filename) {
    echo json_encode(['error' => 'Failed to upload image. Extension not allowed or size too large.']);
    exit;
}

// 2. Return the public URL to Quill
$url = base_url('uploads/tournaments/' . $uploaded_filename);
echo json_encode(['url' => $url]);
