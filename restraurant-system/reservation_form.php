<?php
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
        $res_id = createReservation($pdo, $_SESSION['temp_name'], $_SESSION['temp_email'], 
                                    $_SESSION['temp_phone'], $date, $slot_id, $table_id, $party_size);
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
<html>
<head>
    <title>Reserve Table</title>
    <style>
        body { font-family: sans-serif; max-width: 800px; margin: 20px auto; }
        .form-group { margin-bottom: 15px; }
        .table-option { border: 1px solid #ccc; padding: 10px; margin: 5px 0; cursor: pointer; }
        .table-option:hover { background: #f0f0f0; }
        .hidden { display: none; }
        .error { color: red; }
    </style>
    <script>
        // Simple JS to toggle table selection radio
        function selectTable(id) {
            document.getElementById('selected_table_id').value = id;
        }
    </script>
</head>
<body>
    <h1>Table Reservation</h1>
    <?php if($message): ?><p class="error"><?= htmlspecialchars($message) ?></p><?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Name:</label><input type="text" name="name" required value="<?= $_SESSION['temp_name'] ?? '' ?>">
        </div>
        <div class="form-group">
            <label>Email:</label><input type="email" name="email" required value="<?= $_SESSION['temp_email'] ?? '' ?>">
        </div>
        <div class="form-group">
            <label>Party Size:</label><input type="number" name="party_size" min="1" max="20" required value="<?= $_SESSION['temp_size'] ?? '' ?>">
        </div>
        <div class="form-group">
            <label>Date:</label><input type="date" name="date" required value="<?= $_SESSION['temp_date'] ?? date('Y-m-d') ?>">
        </div>
        <div class="form-group">
            <label>Time Slot:</label>
            <select name="slot_id" required>
                <?php foreach($slots as $slot): ?>
                    <option value="<?= $slot['slot_id'] ?>" <?= (($_SESSION['temp_slot'] ?? 0) == $slot['slot_id']) ? 'selected' : '' ?>>
                        <?= $slot['slot_name'] ?> (<?= $slot['start_time'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if (!empty($available_tables)): ?>
            <h3>Available Tables:</h3>
            <?php foreach($available_tables as $table): ?>
                <div class="table-option" onclick="selectTable(<?= $table['table_id'] ?>)">
                    <input type="radio" name="table_id" value="<?= $table['table_id'] ?>" id="t_<?= $table['table_id'] ?>" required>
                    <label for="t_<?= $table['table_id'] ?>">Table <?= htmlspecialchars($table['table_number']) ?> (Capacity: <?= $table['capacity'] ?>)</label>
                </div>
            <?php endforeach; ?>
            <input type="hidden" id="selected_table_id" name="table_id" value="">
            <button type="submit" name="confirm_payment">Confirm & Pay (Simulated)</button>
        <?php else: ?>
            <button type="submit">Check Availability</button>
        <?php endif; ?>
    </form>
</body>
</html>