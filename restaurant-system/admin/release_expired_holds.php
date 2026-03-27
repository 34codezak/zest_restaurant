<?php
/**
 * Release expired reservation holds
 * Can be run via browser or cron job
 */

require_once __DIR__ . '/../config/db.php';

try {
    // Ensure PDO exists
    if (!$pdo) {
        throw new Exception("Database connection not available.");
    }

    // Update expired holds
    $stmt = $pdo->prepare("
        UPDATE reservations
        SET status = 'cancelled'
        WHERE status = 'held'
        AND hold_expires_at IS NOT NULL
        AND hold_expires_at <= NOW()
    ");

    $stmt->execute();

    $affectedRows = $stmt->rowCount();

    // Response
    $message = [
        'success' => true,
        'released_count' => $affectedRows,
        'timestamp' => date('Y-m-d H:i:s')
    ];

    // Output depending on environment
    if (php_sapi_name() === 'cli') {
        echo "Expired holds released: {$affectedRows}\n";
    } else {
        header('Content-Type: application/json');
        echo json_encode($message);
    }

} catch (PDOException $e) {

    $error = [
        'success' => false,
        'error' => 'Database error',
        'message' => $e->getMessage()
    ];

    http_response_code(500);
    echo json_encode($error);

} catch (Exception $e) {

    $error = [
        'success' => false,
        'error' => 'General error',
        'message' => $e->getMessage()
    ];

    http_response_code(500);
    echo json_encode($error);
}