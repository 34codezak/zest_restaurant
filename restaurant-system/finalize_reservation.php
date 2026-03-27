<?php
require_once 'config/db.php';

$reservation_id = $_POST['reservation_id'];

$stmt = $pdo->prepare("
    UPDATE reservations
    SET status = 'confirmed', hold_expires_at = NULL
    WHERE id = ?
    AND status = 'held'
    AND hold_expires_at > NOW()
");

$stmt->execute([$reservation_id]);

if ($stmt->rowCount() > 0) {
    header("Location: index.php?status=success");
} else {
    header("Location: index.php?status=expired");
}
exit();