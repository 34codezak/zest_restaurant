<?php
require_once '../config/session.php';

require_once __DIR__ . '/config.php';

$reservation_id = (int)$_POST['reservation_id'];

$stmt = $pdo->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = ?");
$stmt->execute([$reservation_id]);

header("Location: admin_reservations.php");
exit;
?>
