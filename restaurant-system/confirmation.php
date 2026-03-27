<?php
require_once __DIR__ . '/session.php';

require_once __DIR__ . '/config.php';

$reservation_id = (int)$_POST['reservation_id'];

$stmt = $pdo->prepare("
    UPDATE reservations
    SET status = 'confirmed', hold_expires_at = NULL
    WHERE id = ? AND status = 'held' AND hold_expires_at > NOW()
");
$stmt->execute([$reservation_id]);

if ($stmt->rowCount() > 0) {
    header("Location: simulated_payment.php?id=" . $reservation_id);
    exit();
} else {
    header("Location: reservation_form.php");
    exit();
}
?>
