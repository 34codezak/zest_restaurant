<?php
session_start();

// Clear user session
$_SESSION = array();

// Clear remember me cookie if set
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    unset($_COOKIE['remember_token']);
}

// Destroy session
session_destroy();

// Redirect to homepage
header("Location: index.php");
exit;
?>