<?php
require_once 'config.php';

// Handle Cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_id'])) {
    $stmt = $pdo->prepare("UPDATE reservations SET status = 'cancelled' WHERE reservation_id = :id");
    $stmt->execute([':id' => $_POST['cancel_id']]);
    $msg = "Reservation Cancelled";
}

// Fetch Reservations
$sql = "SELECT r.*, c.name as customer_name, t.table_number, ts.slot_name 
        FROM reservations r 
        JOIN customers c ON r.customer_id = c.customer_id 
        JOIN tables t ON r.table_id = t.table_id 
        JOIN time_slots ts ON r.slot_id = ts.slot_id 
        ORDER BY r.reservation_date DESC, ts.start_time DESC";
$reservations = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html>
<head><title>Zest Admin Dashboard</title></head>
<body>
    <h1>Admin Reservation View</h1>
    <?php if(isset($msg)) echo "<p style='color:green'>$msg</p>"; ?>
    <a href="index.php">Back to Home</a>
    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th><th>Customer</th><th>Date</th><th>Time</th><th>Table</th><th>Size</th><th>Status</th><th>Action</th>
        </tr>
        <?php foreach($reservations as $res): ?>
        <tr>
            <td><?= $res['reservation_id'] ?></td>
            <td><?= htmlspecialchars($res['customer_name']) ?></td>
            <td><?= $res['reservation_date'] ?></td>
            <td><?= $res['slot_name'] ?></td>
            <td><?= $res['table_number'] ?></td>
            <td><?= $res['party_size'] ?></td>
            <td><?= $res['status'] ?></td>
            <td>
                <?php if($res['status'] != 'cancelled'): ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="cancel_id" value="<?= $res['reservation_id'] ?>">
                    <button type="submit" onclick="return confirm('Cancel this reservation?')">Cancel</button>
                </form>
                <?php else: ?>
                -
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>