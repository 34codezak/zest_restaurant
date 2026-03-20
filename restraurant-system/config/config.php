<?php
// config.php - Handles DB connection using PDO to prevent SQL Injection.

// 1. Security: Disable error display in production
if (getenv('APP_ENV') !== 'production') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    // Log errors to file instead
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/logs/php_errors.log');
}

// 2. Configuration (use environment variables in production)
$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'restaurant_system';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$charset = 'utf8mb4'; // ✅ Defined BEFORE use in DSN

// 3. PDO DSN & Options
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Throw exceptions on error
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Return associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                   // Use real prepared statements
    PDO::ATTR_PERSISTENT         => false,                   // Disable persistent connections
];

// 4. Connection with Singleton Pattern (prevent multiple connections)
function getPDO(): PDO {
    static $pdo = null;
    
    if ($pdo === null) {
        global $dsn, $user, $pass, $options;
        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            // Log error, don't expose details to user
            error_log("DB Connection Error: " . $e->getMessage());
            throw new \PDOException("Database connection failed", 500);
        }
    }
    return $pdo;
}

// 5. Session Management (with safety check)
if (session_status() === PHP_SESSION_NONE) {
    // Secure session settings
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    if (getenv('APP_ENV') === 'production') {
        ini_set('session.cookie_secure', 1);
    }
    session_start();
}
?>