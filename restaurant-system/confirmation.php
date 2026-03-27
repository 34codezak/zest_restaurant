<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/db.php';
require_once 'config/session.php';

// Get reservation ID from query string
$reservation_id = $_GET['id'] ?? null;

if (!$reservation_id || !is_numeric($reservation_id)) {
    die("Invalid reservation ID");
}

try {
    // Prepare the query
    $stmt = $pdo->prepare("
        SELECT r.reservation_id, r.reservation_date, r.party_size, r.status, r.hold_expires_at,
               c.name AS customer_name, c.email, c.phone,
               t.table_number, t.capacity
        FROM reservations r
        JOIN customers c ON r.customer_id = c.customer_id
        JOIN tables t ON r.table_id = t.table_id
        WHERE r.reservation_id = ?
    ");

    // Execute with bound parameter
    $stmt->execute([$reservation_id]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
    // Redirect if reservation not found
    header("Location: successinfo.php?error=notfound");
    exit();
}

// Redirect to successinfo.php with reservation ID in query string
header("Location: successinfo.php?reservation_id=" . urlencode($reservation['reservation_id']));
exit();

    if ($reservation['status'] === 'held') {
        echo "<p><em>Please complete your payment before " . htmlspecialchars($reservation['hold_expires_at']) . "</em></p>";
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}