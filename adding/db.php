<?php
// define('DB_HOST', 'localhost');
// define('DB_NAME', 'v4d');
// define('DB_USER', 'root');
// define('DB_PASS', '');

define('DB_HOST', 'sql102.infinityfree.com');
define('DB_NAME', 'if0_41380504_v4d');
define('DB_USER', 'if0_41380504');
define('DB_PASS', 'dGWUN9ZJWnZNhBA');

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