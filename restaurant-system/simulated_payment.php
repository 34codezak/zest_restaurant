<?php
// simulated_payment.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/session.php';
require_once 'config/db.php';

// Get reservation ID from URL
$reservation_id = (int)($_GET['id'] ?? 0);

if ($reservation_id <= 0) {
    die("<h2 style='color:#ff6600; text-align:center;'>Invalid reservation ID.</h2>");
}

// Optional: check if reservation exists
$stmt = $pdo->prepare("SELECT status FROM reservations WHERE reservation_id = ?");
$stmt->execute([$reservation_id]);
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    die("<h2 style='color:#ff6600; text-align:center;'>Reservation not found.</h2>");
}

// If form submitted, mark reservation as confirmed
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE reservations SET status = 'confirmed' WHERE reservation_id = ?");
    $stmt->execute([$reservation_id]);

    // Redirect to success page
    header("Location: successinfo.php?reservation_id=" . $reservation_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulate Payment - Zest Restaurant</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9dddd;
            color: #130c00;
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .reservation-section {
            width: 100%;
            display: flex;
            justify-content: center;
        }
        .reservation-card {
            background-color: #f9dddd;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(255, 153, 0, 0.5);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        .reservation-header h3 {
            margin: 0 0 20px 0;
            color: #ffae00;
        }
        .reservation-body p.info-text {
            margin-bottom: 25px;
            font-size: 16px;
            color: #f8ab11;
        }
        button.btn-submit {
            background-color: #e5c33c;
            color: #1a1a1a;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }
        button.btn-submit:hover {
            background-color: #e5c33c;
        }
    </style>
</head>
<body>

<main class="reservation-section">
    <div class="reservation-card">
        <div class="reservation-header">
            <h3>Payment Simulation</h3>
        </div>
        <div class="reservation-body">
            <p class="info-text">Your reservation is temporarily held for 10 minutes.</p>

            <form action="" method="POST" id="payment-form">
                <input type="hidden" name="reservation_id" value="<?php echo $reservation_id; ?>">
                <button type="submit" class="btn-submit"><i class="fas fa-credit-card"></i> Complete Payment</button>
            </form>
        </div>
    </div>
</main>

</body>
</html>