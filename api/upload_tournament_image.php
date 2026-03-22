<?php
require_once __DIR__ . '/../adding/db.php';
require_once __DIR__ . '/../adding/functions.php';
session_start();

header('Content-Type: application/json');

if (!is_admin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['image'])) {
    echo json_encode(['error' => 'No image uploaded', 'debug_files' => $_FILES]);
    exit;
}

$file = $_FILES['image'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$temp_path = $file['tmp_name'];

// Check extension explicitly if handle_upload is failing
$allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
if (!in_array($ext, $allowed_ext)) {
    echo json_encode(['error' => "Extension .$ext not allowed", 'allowed' => $allowed_ext]);
    exit;
}

// Check if PHP's fileinfo extension is misreading the MIME type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $temp_path);
finfo_close($finfo);

// Let's bypass strict MIME check if it's already a known extension for now, 
// OR log what's happening to find the culprit.
$uploaded_filename = handle_upload('image', __DIR__ . '/../uploads/tournaments/', $allowed_ext, 10 * 1024 * 1024);

if (!$uploaded_filename) {
    // Detailed error reporting
    $error_msg = "PHP Upload Error Code: " . $file['error'];
    if ($file['size'] > 10 * 1024 * 1024) $error_msg = "File too large (" . $file['size'] . " bytes)";
    
    echo json_encode([
        'error' => "Upload failed. $error_msg",
        'debug' => [
            'name' => $file['name'],
            'mime_detected' => $mime,
            'extension' => $ext,
            'size' => $file['size'],
            'tmp_name_exists' => file_exists($temp_path)
        ]
    ]);
    exit;
}

$url = base_url('uploads/tournaments/' . $uploaded_filename);
echo json_encode(['url' => $url]);
