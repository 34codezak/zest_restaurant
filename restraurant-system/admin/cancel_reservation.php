<?php
require_once "config/db.php";

$reservation_id = (int)$_POST['reservation_id'];

$stmt = $pdo->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = ?");
$stmt->execute([$reservation_id]);

header("Location: admin_reservations.php");
exit;
?>
