<?php
require_once "config/db.php";

$sql = "
SELECT r.id, c.full_name, c.email, c.phone, t.table_number, r.reservation_date, ts.slot_time, r.party_size, r.status
FROM reservations r
JOIN customers c ON r.customer_id = c.id
JOIN tables t ON r.table_id = t.id
JOIN time_slots ts ON r.time_slot_id = ts.id
ORDER BY r.reservation_date, ts.slot_time
";

$stmt = $pdo->query($sql);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Reservations</title>
</head>
<body>
    <h2>Admin - All Reservations</h2>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Table</th>
            <th>Date</th>
            <th>Time</th>
            <th>Party Size</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach ($reservations as $r): ?>
        <tr>
            <td><?php echo $r['id']; ?></td>
            <td><?php echo htmlspecialchars($r['full_name']); ?></td>
            <td><?php echo htmlspecialchars($r['email']); ?></td>
            <td><?php echo htmlspecialchars($r['phone']); ?></td>
            <td><?php echo $r['table_number']; ?></td>
            <td><?php echo $r['reservation_date']; ?></td>
            <td><?php echo $r['slot_time']; ?></td>
            <td><?php echo $r['party_size']; ?></td>
            <td><?php echo $r['status']; ?></td>
            <td>
                <?php if ($r['status'] !== 'cancelled'): ?>
                    <form action="cancel_reservation.php" method="POST">
                        <input type="hidden" name="reservation_id" value="<?php echo $r['id']; ?>">
                        <button type="submit">Cancel</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
