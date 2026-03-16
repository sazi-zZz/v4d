<?php
$host_no_port = explode(':', $_SERVER['HTTP_HOST'] ?? '')[0];
$is_local = in_array($host_no_port, ['localhost', '127.0.0.1']);

if ($is_local) {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'v4d');
    define('DB_USER', 'root');
    define('DB_PASS', '');
}
else {
    // Production (InfinityFree) - These will be replaced by GitHub Actions
    define('DB_HOST', 'DB_HOST_PLACEHOLDER');
    define('DB_NAME', 'DB_NAME_PLACEHOLDER');
    define('DB_USER', 'DB_USER_PLACEHOLDER');
    define('DB_PASS', 'DB_PASS_PLACEHOLDER');
}

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
        );

    // Auto-migrate columns if they don't exist
    try {
        $pdo->exec("ALTER TABLE players ADD COLUMN username VARCHAR(50) UNIQUE DEFAULT NULL");
    }
    catch (PDOException $e) {
    }

    try {
        $pdo->exec("ALTER TABLE players ADD COLUMN password_hash VARCHAR(255) DEFAULT NULL");
    }
    catch (PDOException $e) {
    }

}
catch (PDOException $e) {
    die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
}