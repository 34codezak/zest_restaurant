<?php

session_start();
require_once "config/db.php";

$full_name = trim($_POST['full_name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$reservation_date = $_POST['reservation_date'];
$reservation_time = $_POST['reservation_time'];
$party_size = (int)$_POST['party_size'];

$errors = [];

// Validating the user inputs
if ($party_size < 1 || $party_size > 8) {
    $errors[] = "Party size must be between 1 and 8";
}

if (strtotime($reservation_date) < strtotime(date('Y-m-d'))) {
    $errors[] = "Reservation date cannot be in the past.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email address";
}

if (empty($full_name) || empty($phone)) {
    $errors[] = "Name and phone number are required.";
}

if (!empty($errors)) {
    foreach($errors as $error) {
        echo "<p style='color: red;'>$error</p>";
    }
    echo "<a href='reservation_form.php'>Go Back</a>";
    exit;
}

// Find time slot
$stmt = $pdo->prepare("SELECT id FROM time_slots WHERE slot_time = ?");
$stmt->execute([$reservation_time]);
$slot = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$slot) {
    echo "Invalid time slot selected.";
    exit;
}

$time_slot_id = $slot['id'];

// Find available tables with enough capacity and not already reserved
$sql = "
SELECT t.id, t.table, t.capacity
FROM tables t 
WHERE t.capacity >= ?
AND t.id NOT IN (
    SELET r.table_id
    FROM reservations r
    WHERE r.reservation_date = ? 
    AND r.time_slot_id = ?
    AND r.status IN ('held', 'confirmed')
    AND (
        r.hold_expires_at IS NULL OR r.hold_expires_at > NOW()
    )
)
ORDER BY t.capacity ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$party_size, $reservation_date, $time_slot_id]);
$available_tables = $stmt->fetchAll(PDO::FETCH_ASSOC);

$_SESSION['customer_data'] = [
    'full_name' => $full_name,
    'email' => $email,
    'phone' => $phone,
    'reservation_date' =>  $reservation_date,
    'reservation_time' => $reservation_time,
    'time_slot_id' => $time_slot_id,
    'party_size' => $party_size
];

echo "<h2>Available Tables</h2>";

if (count($available_tables) > 0) {
    echo "<form action='create_reservation.php' method='POST'>";
    foreach ($available_tables as $table) {
        echo "<input type='radio' name='table_id' value='{$table['id']}' required> ";
        echo "Table {$table['table_number']} (Capacity: {$table['capacity']})<br>";
    }
    echo "<br><button type='submit'>Reserve Selected Table</button>";
    echo "</form>";
} else {
    echo "<p style='color:red;'>No tables available for the selected time and party size.</p>";
    echo "<a href='reservation_form.php'>Try Another Time</a>";
}

?>