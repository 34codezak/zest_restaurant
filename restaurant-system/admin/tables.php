<?php
require_once '../config/session.php';

require_once __DIR__ . '/config.php';

// Restrict access
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin/admin_login.php");
    exit();
}

$msg = "";

// 🔴 Release Table (cancel reservation)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['release_id'])) {
    $stmt = $pdo->prepare("UPDATE reservations SET status = 'cancelled' WHERE reservation_id = :id");
    $stmt->execute(['id' => $_POST['release_id']]);
    $msg = "Table released successfully.";
}

// 🟢 Mark as Paid
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_paid'])) {
    $stmt = $pdo->prepare("UPDATE reservations SET is_paid = 1 WHERE reservation_id = :id");
    $stmt->execute(['id' => $_POST['mark_paid']]);
    $msg = "Marked as paid.";
}

// 📊 Fetch all tables + latest reservation
$sql = "
SELECT 
    t.table_id,
    t.table_number,
    t.capacity,
    r.reservation_id,
    r.reservation_date,
    r.status,
    r.is_paid,
    ts.start_time,
    ts.end_time
FROM tables t
LEFT JOIN reservations r ON t.table_id = r.table_id
LEFT JOIN time_slots ts ON r.slot_id = ts.slot_id
ORDER BY t.table_number ASC
";

$tables = $pdo->query($sql)->fetchAll();

// Current datetime
$now = new DateTime();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Tables</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        th { background: #333; color: #fff; }
        .free { background: #d4edda; }
        .occupied { background: #fff3cd; }
        .expired { background: #f8d7da; }
    </style>
</head>
<body>

<h2>🍽️ Table Management</h2>

<?php if ($msg): ?>
    <p style="color: green;"><strong><?= $msg ?></strong></p>
<?php endif; ?>

<table>
    <tr>
        <th>Table #</th>
        <th>Capacity</th>
        <th>Date</th>
        <th>Time</th>
        <th>Status</th>
        <th>Payment</th>
        <th>Action</th>
    </tr>

<?php foreach ($tables as $t): 

    $rowClass = "free";
    $statusText = "Available";

    if ($t['reservation_id']) {

        $reservationTime = new DateTime($t['reservation_date'] . ' ' . $t['end_time']);

        if ($t['status'] === 'cancelled') {
            $statusText = "Cancelled";
            $rowClass = "free";
        } elseif ($reservationTime < $now) {
            $statusText = "Expired";
            $rowClass = "expired";
        } else {
            $statusText = ucfirst($t['status']);
            $rowClass = "occupied";
        }
    }
?>

<tr class="<?= $rowClass ?>">
    <td><?= $t['table_number'] ?></td>
    <td><?= $t['capacity'] ?></td>
    <td><?= $t['reservation_date'] ?? '-' ?></td>
    <td><?= $t['start_time'] ?? '-' ?></td>
    <td><?= $statusText ?></td>
    <td>
        <?php if ($t['reservation_id']): ?>
            <?= $t['is_paid'] ? "Paid" : "Unpaid" ?>
        <?php else: ?>
            -
        <?php endif; ?>
    </td>

    <td>
        <?php if ($t['reservation_id'] && $t['status'] !== 'cancelled'): ?>
            
            <!-- Release -->
            <form method="POST" style="display:inline;">
                <input type="hidden" name="release_id" value="<?= $t['reservation_id'] ?>">
                <button type="submit">Release</button>
            </form>

            <!-- Mark Paid -->
            <?php if (!$t['is_paid']): ?>
            <form method="POST" style="display:inline;">
                <input type="hidden" name="mark_paid" value="<?= $t['reservation_id'] ?>">
                <button type="submit">Mark Paid</button>
            </form>
            <?php endif; ?>

        <?php else: ?>
            -
        <?php endif; ?>
    </td>
</tr>

<?php endforeach; ?>

</table>

</body>
</html>