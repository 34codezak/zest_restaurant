<?php
// successinfo.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/db.php';
require_once 'config/session.php';

$reservation_id = $_GET['reservation_id'] ?? null;
$error = $_GET['error'] ?? null;

if ($error === 'notfound') {
    die("<h2 style='color:#ff6600; text-align:center;'>Reservation not found.</h2>");
}

if (!$reservation_id || !is_numeric($reservation_id)) {
    die("<h2 style='color:#ff6600; text-align:center;'>Invalid reservation ID.</h2>");
}

try {
    $stmt = $pdo->prepare("
        SELECT r.reservation_id, r.reservation_date, r.party_size, r.status, r.hold_expires_at,
               c.name AS customer_name, c.email, c.phone,
               t.table_number, t.capacity
        FROM reservations r
        JOIN customers c ON r.customer_id = c.customer_id
        JOIN tables t ON r.table_id = t.table_id
        WHERE r.reservation_id = ?
    ");

    $stmt->execute([$reservation_id]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        die("<h2 style='color:#ff6600; text-align:center;'>Reservation not found.</h2>");
    }

} catch (PDOException $e) {
    die("<h2 style='color:#ff6600; text-align:center;'>Database error: " . htmlspecialchars($e->getMessage()) . "</h2>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reservation Confirmation</title>
<style>
    body {
        background-color: #efeeee; 
        color: #0d0800; 
        font-family: 'Arial', sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
    }
    .container {
        background-color: #f9dddd;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 0 20px rgba(255, 153, 0, 0.5);
        max-width: 500px;
        width: 100%;
    }
    h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #ff6600;
    }
    p {
        margin: 8px 0;
        font-size: 16px;
    }
    strong {
        color: #ff9900;
    }
    em {
        color: #ff3300;
        font-weight: bold;
    }
    .status-held {
        background-color: #333;
        padding: 10px;
        border-left: 5px solid #ff6600;
        margin-top: 15px;
        border-radius: 5px;
    }
    a.button {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #ff6600;
        color: #1a1a1a;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        text-align: center;
    }
    a.button:hover {
        background-color: #ff9900;
        color: #000;
    }
</style>
</head>
<body>
<div class="container">
    <h2>Reservation Confirmation</h2>
    <p><strong>Reservation ID:</strong> <?= htmlspecialchars($reservation['reservation_id']) ?></p>
    <p><strong>Name:</strong> <?= htmlspecialchars($reservation['customer_name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($reservation['email']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($reservation['phone']) ?></p>
    <p><strong>Table Number:</strong> <?= htmlspecialchars($reservation['table_number']) ?></p>
    <p><strong>Party Size:</strong> <?= htmlspecialchars($reservation['party_size']) ?></p>
    <p><strong>Date:</strong> <?= htmlspecialchars($reservation['reservation_date']) ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars($reservation['status']) ?></p>

    <?php if ($reservation['status'] === 'held'): ?>
        <div class="status-held">
            <em>Please complete your payment before <?= htmlspecialchars($reservation['hold_expires_at']) ?></em>
        </div>
    <?php endif; ?>

    <a class="button" href="simulated_payment.php?id=<?php echo $reservation_id; ?>">Confirm & Pay</a>
    <a class="button" href="index.php">Back to Home</a>
</div>
</body>
</html>