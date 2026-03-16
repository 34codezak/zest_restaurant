<?php
require_once "config/db.php";

$reservation_id = (int)$_POST['reservation_id'];

$stmt = $pdo->prepare("
    UPDATE reservations
    SET status = 'confirmed', hold_expires_at = NULL
    WHERE id = ? AND status = 'held' AND hold_expires_at > NOW()
");
$stmt->execute([$reservation_id]);

if ($stmt->rowCount() > 0) {
    echo "<h2>Reservation Confirmed</h2>";
    echo "<p>Your reservation has been successfully confirmed.</p>";
    echo "<p>Reservation ID: " . $reservation_id . "</p>";
} else {
    echo "<h2>Reservation Failed</h2>";
    echo "<p>The hold expired or the reservation is no longer available.</p>";
}
?>
