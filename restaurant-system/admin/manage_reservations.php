<?php
// manage_reservations.php
session_start();
require_once '../config/db.php'; 
require_once '../config/session.php'; // Admin session check

// Ensure only logged-in admins can access
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: admin_login.php");
    exit();
}

// Handle reservation actions (Confirm / Cancel)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['reservation_id'])) {
    $reservation_id = (int)$_POST['reservation_id'];
    $action = $_POST['action'];

    if ($action === 'confirm') {
        $stmt = $pdo->prepare("UPDATE reservations SET status = 'confirmed' WHERE reservation_id = ?");
        $stmt->execute([$reservation_id]);
    } elseif ($action === 'cancel') {
        $stmt = $pdo->prepare("UPDATE reservations SET status = 'cancelled' WHERE reservation_id = ?");
        $stmt->execute([$reservation_id]);
    }
    header("Location: manage_reservations.php"); // Refresh page
    exit();
}

// Fetch all reservations
$stmt = $pdo->query("
    SELECT r.reservation_id, r.reservation_date, r.party_size, r.status, r.hold_expires_at,
           c.name AS customer_name, c.email, c.phone,
           t.table_number
    FROM reservations r
    JOIN customers c ON r.customer_id = c.customer_id
    JOIN tables t ON r.table_id = t.table_id
    ORDER BY r.reservation_date DESC, r.reservation_id DESC
");
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Manage Reservations</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #1e1e1e;
        color: #fff;
        margin: 0;
        padding: 0;
        align-items: center;
    }
    h1 {
        text-align: center;
        padding: 20px 0;
        color: #ff9800;
    }
    table {
        width: 95%;
        margin: 20px auto;
        border-collapse: collapse;
    }
    th, td {
        padding: 12px;
        border: 1px solid #333;
        text-align: center;
    }
    th {
        background-color: #ff9800;
        color: #1e1e1e;
    }
    tr:nth-child(even) {
        background-color: #2c2c2c;
    }
    tr:hover {
        background-color: #444;
    }
    .btn {
        padding: 6px 12px;
        margin: 2px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        color: #fff;
        font-weight: bold;
    }

    .button {
        background: #e5c33e;
        color: #000;
        text-decoration: none;
        padding: 10px;
        justify-content: right;
    }
    .btn-confirm {
        background-color: #4caf50;
    }
    .btn-cancel {
        background-color: #f44336;
    }
    .btn-edit {
        background-color: #ff9800;
        color: #1e1e1e;
    }
    form {
        display: inline;
    }
</style>
</head>
<body>

<h1>Manage Reservations</h1>

<table>
    <tr>
        <th>ID</th>
        <th>Customer</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Table</th>
        <th>Party Size</th>
        <th>Date</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($reservations as $res): ?>
    <tr>
        <td><?= htmlspecialchars($res['reservation_id']) ?></td>
        <td><?= htmlspecialchars($res['customer_name']) ?></td>
        <td><?= htmlspecialchars($res['email']) ?></td>
        <td><?= htmlspecialchars($res['phone']) ?></td>
        <td><?= htmlspecialchars($res['table_number']) ?></td>
        <td><?= htmlspecialchars($res['party_size']) ?></td>
        <td><?= htmlspecialchars($res['reservation_date']) ?></td>
        <td><?= htmlspecialchars($res['status']) ?></td>
        <td>
            <?php if ($res['status'] === 'held' || $res['status'] === 'pending_payment'): ?>
            <form method="POST">
                <input type="hidden" name="reservation_id" value="<?= $res['reservation_id'] ?>">
                <input type="hidden" name="action" value="confirm">
                <button class="btn btn-confirm" type="submit">Confirm</button>
            </form>
            <?php endif; ?>
            <?php if ($res['status'] !== 'cancelled'): ?>
            <form method="POST">
                <input type="hidden" name="reservation_id" value="<?= $res['reservation_id'] ?>">
                <input type="hidden" name="action" value="cancel">
                <button class="btn btn-cancel" type="submit">Cancel</button>
            </form>
            <?php endif; ?>
            <form method="GET" action="edit_reservation.php">
                <input type="hidden" name="reservation_id" value="<?= $res['reservation_id'] ?>">
                <button class="btn btn-edit" type="submit">Edit</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
        <a href="release_expired_holds.php" class="button">← Expired Holds</a>
</body>
</html>