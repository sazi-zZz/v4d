<?php
/**
 * view_asset.php - Securely serves assets from outside the web root.
 */

// 1. Identify the file from the URL parameter
$requested_file = $_GET['file'] ?? '';

if (!$requested_file) {
    header("HTTP/1.0 400 Bad Request");
    exit("File parameter is missing.");
}

// 2. Normalize and check for directory traversal
$requested_file = str_replace(['../', './', '..\\', '.\\'], '', $requested_file);
$requested_file = ltrim($requested_file, '/\\');

// 3. Define the base path
$server_path = $_SERVER['DOCUMENT_ROOT'] ?? '';
$dir_path    = __DIR__;

$possible_paths = [
    realpath($dir_path . '/../uploads_v4d'),
    dirname($dir_path) . '/uploads_v4d',
    realpath($dir_path . '/uploads_v4d'),    // Inside current folder (htdocs)
    $dir_path . '/uploads_v4d',               // Direct relative check
    // Absolute paths from root (typical InfinityFree/cPanel)
    dirname($server_path) . '/uploads_v4d',
    $server_path . '/../uploads_v4d',
];

$base_path = null;
$full_path = null;

foreach ($possible_paths as $path) {
    if (empty($path)) continue;
    $test_path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $requested_file;
    if (file_exists($test_path) && is_file($test_path)) {
        $base_path = $path;
        $full_path = $test_path;
        break;
    }
}

// DEBUG MODE: ENABLED for troubleshooting
if (isset($_GET['debug'])) {
    $server_path = $_SERVER['DOCUMENT_ROOT'] ?? 'Not set';
    $dir_path    = __DIR__;
    echo "<h1>v4d Asset Debug</h1>";
    echo "<b>Requested:</b> " . htmlspecialchars($requested_file) . "<br>";
    echo "<b>Doc Root:</b> " . htmlspecialchars($server_path) . "<br>";
    echo "<b>__DIR__:</b> " . htmlspecialchars($dir_path) . "<br>";
    echo "<b>Searching in:</b><pre>";
    foreach ($possible_paths as $p) {
        $exists = ($p && file_exists($p)) ? "[EXISTS]" : "[MISSING]";
        echo "$exists $p\n";
    }
    echo "</pre>";
    if ($full_path) echo "<b>FOUND AT:</b> " . htmlspecialchars($full_path);
    else echo "<b>NOT FOUND ANYWHERE</b>";
    
    // Check folder permissions if found
    if ($base_path && file_exists($base_path)) {
        echo "<h2>Folder Info</h2>";
        echo "Base folder permissions: " . substr(sprintf('%o', fileperms($base_path)), -4) . "<br>";
        echo "Is Readable: " . (is_readable($base_path) ? "Yes" : "No") . "<br>";
    }
    exit;
}

if ($full_path) {
    // 4. Detect MIME type
    $mime = null;
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $full_path);
        finfo_close($finfo);
    } 
    
    if (!$mime && function_exists('mime_content_type')) {
        $mime = mime_content_type($full_path);
    }

    if (!$mime) {
        $ext = strtolower(pathinfo($full_path, PATHINFO_EXTENSION));
        $mimes = [
            'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png', 'gif' => 'image/gif',
            'webp' => 'image/webp', 'ico' => 'image/x-icon'
        ];
        $mime = $mimes[$ext] ?? 'application/octet-stream';
    }
    
    // 5. Serve
    header("Content-Type: $mime");
    header("Content-Length: " . filesize($full_path));
    header("Cache-Control: public, max-age=86400"); // 1 day cache
    readfile($full_path);
    exit;
} else {
    header("HTTP/1.0 404 Not Found");
    exit("Asset not found.");
}
