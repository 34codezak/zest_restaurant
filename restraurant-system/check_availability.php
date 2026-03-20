<?php
// functions.php
require_once 'config.php';

// Validate Party Size
function validatePartySize($size) {
    return is_numeric($size) && $size > 0 && $size <= 20;
}

// Check Availability Algorithm
function getAvailableTables($pdo, $date, $slot_id, $party_size) {
    // 1. Find tables with enough capacity
    // 2. Exclude tables that already have a confirmed reservation for this date/slot
    $sql = "SELECT t.table_id, t.table_number, t.capacity 
            FROM tables t 
            WHERE t.capacity >= :size 
            AND t.table_id NOT IN (
                SELECT r.table_id 
                FROM reservations r 
                WHERE r.reservation_date = :date 
                AND r.slot_id = :slot_id 
                AND r.status != 'cancelled'
            )";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':size' => $party_size,
        ':date' => $date,
        ':slot_id' => $slot_id
    ]);
    
    return $stmt->fetchAll();
}

// Create Customer & Reservation (Transaction for Safety)
function createReservation($pdo, $name, $email, $phone, $date, $slot_id, $table_id, $party_size) {
    try {
        $pdo->beginTransaction();

        // 1. Create/Find Customer
        $stmt = $pdo->prepare("INSERT INTO customers (name, email, phone) VALUES (:name, :email, :phone)");
        $stmt->execute([':name' => $name, ':email' => $email, ':phone' => $phone]);
        $customer_id = $pdo->lastInsertId();

        // 2. Create Reservation
        $stmt = $pdo->prepare("INSERT INTO reservations (customer_id, table_id, slot_id, reservation_date, party_size, status) 
                               VALUES (:cid, :tid, :sid, :date, :size, 'confirmed')");
        $stmt->execute([
            ':cid' => $customer_id,
            ':tid' => $table_id,
            ':sid' => $slot_id,
            ':date' => $date,
            ':size' => $party_size
        ]);

        $pdo->commit();
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}
?>