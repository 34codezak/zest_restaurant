<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulate Payment - Zest Restaurant</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <?php
    require_once 'config/session.php';

    require_once __DIR__ . '/config.php';
    $reservation_id = (int)$_GET['id'];
    ?>

    <main class="reservation-section">
        <div class="reservation-card">
            <div class="reservation-header">
                <h3>Payment Simulation</h3>
            </div>
            <div class="reservation-body">
                <p class="info-text">Your reservation is temporarily held for 10 minutes.</p>

                <form action="confirmation.php" method="POST" id="payment-form">
                    <input type="hidden" name="reservation_id" value="<?php echo $reservation_id; ?>">
                    <button type="submit" class="btn btn-submit">Complete Payment</button>
                </form>
            </div>
        </div>
    </main>

</body>
</html>