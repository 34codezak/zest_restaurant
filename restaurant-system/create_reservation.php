<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/db.php';
require_once 'config/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit();
}

// Get form data
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';
$guests = (int)($_POST['guests'] ?? 0);

// Validation
if ($guests <= 0) die("Invalid guests");
if (strtotime($date) < strtotime(date('Y-m-d'))) die("Invalid date");

// Get time_slot_id
$stmt = $pdo->prepare("SELECT time_slot_id FROM time_slots WHERE start_time = ?");
$stmt->execute([$time]);
$time_slot_id = $stmt->fetchColumn();

if (!$time_slot_id) die("Invalid time slot");

// Start transaction
$pdo->beginTransaction();

try {

    // Find available table
    $stmt = $pdo->prepare("
        SELECT table_id, capacity
        FROM tables
        WHERE capacity >= ?
        AND table_id NOT IN (
            SELECT table_id FROM reservations
            WHERE reservation_date = ?
            AND time_slot_id = ?
            AND status IN ('held','confirmed')
            AND (hold_expires_at IS NULL OR hold_expires_at > NOW())
        )
        ORDER BY capacity ASC
        LIMIT 1
    ");

    $stmt->execute([$guests, $date, $time_slot_id]);
    $table = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$table) {
        throw new Exception("No available tables");
    }

    // Insert customer
    $stmt = $pdo->prepare("
        INSERT INTO customers (name, email, phone)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$name, $email, $phone]);

    $customer_id = $pdo->lastInsertId();

    // Create reservation
    $stmt = $pdo->prepare("
        INSERT INTO reservations 
        (customer_id, table_id, reservation_date, time_slot_id, status, hold_expires_at)
        VALUES (?, ?, ?, ?, 'held', DATE_ADD(NOW(), INTERVAL 10 MINUTE))
    ");

    $stmt->execute([
        $customer_id,
        $table['table_id'],
        $date,
        $time_slot_id
    ]);

    $reservation_id = $pdo->lastInsertId();

    $pdo->commit();

    header("Location: confirmation.php?id=" . $reservation_id);
    exit();

} catch (Exception $e) {
    $pdo->rollBack();

    echo "ERROR: " . $e->getMessage(); // debug
}