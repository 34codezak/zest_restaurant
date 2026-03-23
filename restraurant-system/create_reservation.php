<?php 

session_start();
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['customer_data'])) {
    die("Session expired. Please start again.");

    $customer = $_SESSION['customer_data'];
    $table_id = (int)$_POST['table_id'];

    try {
        $pdo->beginTransaction();

        // Recheck table availability before insert to avoid race condition
        $checkStmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM reservations
            WHERE table_id = ?
            AND reservation_date = ?
            AND time_slot_id = ?
            AND status IN ('held', 'confirmed')
            AND (hold_expires_at IS NULL OR hold_expires_at > NOW())
        ");
        $checkStmt->execute([$table_id, $customer['reservation_date'], $customer['time_slot_id']]);
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            $pdo->rollBack();
            die("Sorry, the table was just booked by another customer.");
        }


    // Insert customer
    $stmt = $pdo->prepare("INSERT INTO customers (full_name, email, phone) VALUES (?, ?, ?)");
    $stmt->execute([$customer['full_name'], $customer['email'], $customer['phone']]);
    $customer_id = $pdo->lastInsertId();

    // Insert reservation as HELD for 10 minutes
    $stmt = $pdo->prepare("
        INSERT INTO reservations 
        (customer_id, table_id, reservation_date, time_slot_id, party_size, status, hold_expires_at)
        VALUES (?, ?, ?, ?, ?, 'held', DATE_ADD(NOW(), INTERVAL 10 MINUTE))
    ");
    $stmt->execute([
        $customer_id,
        $table_id,
        $customer['reservation_date'],
        $customer['time_slot_id'],
        $customer['party_size']
    ]);

    $reservation_id = $pdo->lastInsertId();
    $pdo->commit();

    header("Location: simulate_payment.php?id=" . $reservation_id);
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    die("Error creating reservation: " . $e->getMessage());
}
}
?>