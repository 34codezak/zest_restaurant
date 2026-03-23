<?php

require_once __DIR__ . '/config.php';

$reservation_id = (int)$_GET['id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Simulate Payment</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>

<h2>Payment Simulation</h2>
<p>Your reservation is temporarily held for 10 minutes.</p>

<form action="confirmation.php" method="POST">
    <input type="hidden" name="reservation_id" value="<?php echo $reservation_id;?>">
     <button type="submit">Complete Payment</button>
    </form>

</body>
</html>