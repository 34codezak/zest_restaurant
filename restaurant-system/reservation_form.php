<?php
require_once 'config/session.php';

require_once 'check_availability.php';

$message = "";
$available_tables = [];

// HANDLE POST (Form Submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $slot_id = $_POST['slot_id'];
    $party_size = $_POST['party_size'];
    $table_id = $_POST['table_id']; // Selected from available list
    
    // Validation
    if (!validatePartySize($party_size)) {
        $message = "Invalid party size.";
    } elseif (isset($_POST['confirm_payment'])) {
        // Simulate Payment Processing & Finalize
        $res_id = createReservation($pdo, 
                                    $_SESSION['temp_name'], 
                                    $_SESSION['temp_email'], 
                                    $_SESSION['temp_phone'], 
                                    $date, 
                                    $slot_id, 
                                    $table_id, 
                                    $party_size
        );
        if($res_id) {
            echo "<h1>Reservation Confirmed! ID: #$res_id</h1><a href='index.php'>Home</a>";
            session_destroy();
            exit;
        } else {
            $message = "Booking failed (possibly double booked). Please try another slot.";
        }
    } else {
        // Step 1: Check Availability
        if (validatePartySize($party_size)) {
            $available_tables = getAvailableTables($pdo, $date, $slot_id, $party_size);
            // Store temp data in session for payment step
            $_SESSION['temp_name'] = $_POST['name'];
            $_SESSION['temp_email'] = $_POST['email'];
            $_SESSION['temp_phone'] = $_POST['phone'];
            $_SESSION['temp_date'] = $date;
            $_SESSION['temp_slot'] = $slot_id;
            $_SESSION['temp_size'] = $party_size;
            
            if(empty($available_tables)) {
                $message = "No tables available for this selection.";
            }
        }
    }
}

// FETCH SLOTS FOR DROPDOWN
$slots = $pdo->query("SELECT * FROM time_slots")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserve Table - Zest Restaurant</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <script>
        function selectTable(id) {
            document.getElementById('selected_table_id').value = id;
            document.querySelectorAll('.table-option').forEach(el => el.classList.remove('selected'));
            document.getElementById('table_' + id).parentElement.classList.add('selected');
        }
    </script>
</head>
<body>

    <main class="reservation-section">
        <div class="reservation-card">
            <div class="reservation-header">
                <h3>Table Reservation</h3>
            </div>
            <div class="reservation-body">

                <?php if (!empty($message)): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <form method="POST" id="table-reservation-form">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" name="name" id="name" required value="<?= $_SESSION['temp_name'] ?? '' ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" required value="<?= $_SESSION['temp_email'] ?? '' ?>">
                    </div>

                    <div class="form-group">
                        <label for="party_size">Party Size:</label>
                        <input type="number" name="party_size" id="party_size" min="1" max="20" required value="<?= $_SESSION['temp_size'] ?? '' ?>">
                    </div>

                    <div class="form-group">
                        <label for="date">Date:</label>
                        <input type="date" name="date" id="date" required value="<?= $_SESSION['temp_date'] ?? date('Y-m-d') ?>">
                    </div>

                    <div class="form-group">
                        <label for="slot_id">Time Slot:</label>
                        <select name="slot_id" id="slot_id" required>
                            <?php foreach ($slots as $slot): ?>
                                <option value="<?= $slot['slot_id'] ?>" <?= (($_SESSION['temp_slot'] ?? 0) == $slot['slot_id']) ? 'selected' : '' ?>>
                                    <?= $slot['slot_name'] ?> (<?= $slot['start_time'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if (!empty($available_tables)): ?>
                        <div class="form-group">
                            <h4>Available Tables:</h4>
                            <?php foreach ($available_tables as $table): ?>
                                <div class="table-option" onclick="selectTable(<?= $table['table_id'] ?>)">
                                    <input type="radio" name="table_radio" value="<?= $table['table_id'] ?>" id="table_<?= $table['table_id'] ?>" required>
                                    <label for="table_<?= $table['table_id'] ?>">
                                        Table <?= htmlspecialchars($table['table_number']) ?> (Capacity: <?= $table['capacity'] ?>)
                                    </label>
                                </div>
                            <?php endforeach; ?>
                            <input type="hidden" id="selected_table_id" name="table_id" value="">
                        </div>

                        <button type="submit" class="btn btn-submit" name="confirm_payment">Confirm &amp; Pay (Simulated)</button>
                    <?php else: ?>
                        <button type="submit" class="btn btn-submit">Check Availability</button>
                    <?php endif; ?>
                </form>

            </div>
        </div>
    </main>

</body>
</html>


<?php

include "db.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){

$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$date = $_POST['date'];
$time = $_POST['time'];
$guests = $_POST['guests'];

# Check if table already reserved

$sql = "SELECT * FROM reservations 
        WHERE reservation_date='$date' 
        AND reservation_time='$time'";

$result = $conn->query($sql);

if($result->num_rows > 0){

echo "<h2>Table Not Available</h2>";
echo "<p>Sorry, this time slot is already reserved.</p>";
echo "<a href='reservation.html'>Try Another Time</a>";

}
else{

$insert = "INSERT INTO reservations 
(name,email,phone,reservation_date,reservation_time,guests)

VALUES('$name','$email','$phone','$date','$time','$guests')";

if($conn->query($insert) === TRUE){

echo "<h2>Reservation Successful</h2>";
echo "<p>Your table has been reserved.</p>";
echo "<a href='reservation.html'>Reserve Another Table</a>";

}else{

echo "Error: ".$conn->error;

}

}

$conn->close();

}

?>