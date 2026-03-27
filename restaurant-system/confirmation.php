<?php
require_once 'config/db.php';

$reservation_id = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT r.*, c.name, t.table_number
    FROM reservations r
    JOIN customers c ON r.customer_id = c.id
    JOIN tables t ON r.table_id = t.id
    WHERE r.id = ?
");
$stmt->execute([$reservation_id]);
$reservation = $stmt->fetch();

if (!$reservation) {
    die("Reservation not found");
}
?>

<h2>Confirm Reservation</h2>
<p>Name: <?= $reservation['name'] ?></p>
<p>Table: <?= $reservation['table_number'] ?></p>

<form action="finalize_reservation.php" method="POST">
    <input type="hidden" name="reservation_id" value="<?= $reservation_id ?>">
    <button type="submit">Confirm Booking</button>
</form>