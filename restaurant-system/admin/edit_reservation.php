<?php
require_once '../config/session.php';
require_once '../config/db.php';

// Protect admin route
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

global $pdo;

// Get reservation ID
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Invalid reservation ID.");
}

// Fetch reservation
$stmt = $pdo->prepare("SELECT * FROM reservations WHERE reservation_id = ?");
$stmt->execute([$id]);
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    die("Reservation not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['customer_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $table = trim($_POST['table_number']);
    $party_size = (int)$_POST['party_size'];
    $date = $_POST['reservation_date'];
    $status = $_POST['status'];

    try {
        $update = $pdo->prepare("
            UPDATE reservations
            SET customer_name = ?, email = ?, phone = ?, 
                table_number = ?, party_size = ?, 
                reservation_date = ?, status = ?
            WHERE reservation_id = ?
        ");

        $update->execute([
            $name, $email, $phone,
            $table, $party_size,
            $date, $status, $id
        ]);

        header("Location: manage_reservations.php?updated=1");
        exit();

    } catch (PDOException $e) {
        die("Update failed: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Reservation</title>
<link rel="stylesheet" href="../assets/style.css">

<style>
body {
    background: #1e1e1e;
    color: #fff;
    font-family: Arial;
}

.container {
    max-width: 600px;
    margin: 50px auto;
}

.card {
    background: #2c2c2c;
    padding: 25px;
    border-radius: 10px;
}

h2 {
    color: #ff9800;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 15px;
}

label {
    color: #ff9800;
}

input, select {
    width: 100%;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #444;
    background: #1e1e1e;
    color: #fff;
}

input:focus, select:focus {
    border-color: #ff9800;
}

.btn {
    padding: 12px;
    background: #ff9800;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    width: 100%;
    font-weight: bold;
}

.btn:hover {
    background: #e68900;
}

.back-link {
    display: block;
    margin-top: 15px;
    text-align: center;
    color: #ccc;
}
</style>

</head>
<body>

<div class="container">
    <div class="card">
        <h2>Edit Reservation</h2>

        <form method="POST">

            <div class="form-group">
                <label>Name</label>
                <input type="text" name="customer_name" value="<?= htmlspecialchars($reservation['customer_name']) ?>">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($reservation['email']) ?>">
            </div>

            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($reservation['phone']) ?>">
            </div>

            <div class="form-group">
                <label>Table</label>
                <input type="text" name="table_number" value="<?= htmlspecialchars($reservation['table_number']) ?>">
            </div>

            <div class="form-group">
                <label>Party Size</label>
                <input type="number" name="party_size" value="<?= $reservation['party_size'] ?>">
            </div>

            <div class="form-group">
                <label>Date</label>
                <input type="datetime-local" name="reservation_date"
                       value="<?= date('Y-m-d\TH:i', strtotime($reservation['reservation_date'])) ?>">
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="pending" <?= $reservation['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="confirmed" <?= $reservation['status'] == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                    <option value="cancelled" <?= $reservation['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    <option value="held" <?= $reservation['status'] == 'held' ? 'selected' : '' ?>>Held</option>
                </select>
            </div>

            <button type="submit" class="btn">Update Reservation</button>
        </form>

        <a href="manage_reservations.php" class="back-link">← Back to Reservations</a>
    </div>
</div>

</body>
</html>