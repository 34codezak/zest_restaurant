<?php
session_start();

// Optionally set session timeout (e.g., 30 minutes)
define('SESSION_TIMEOUT', 1800);

if (isset($_SESSION['last_activity']) && 
    (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit();
}
$_SESSION['last_activity'] = time();
?>