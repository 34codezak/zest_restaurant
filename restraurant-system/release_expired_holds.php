<?php
// This is cleanup logic to release expired held reservations.

require_once __DIR__ . '/config.php';

$stmt = $pdo->prepare("
    UPDATE reservations
    SET status = 'cancelled'
    WHERE status = 'held' AND hold_expires_at <= NOW()
");
$stmt->execute();

echo "Expired reservation holds released successfully.";
?>
