<?php
// Include database connection
require 'db_connect.php'; // Ensure the path to db_connect.php is correct

// Set the content type to JSON
header('Content-Type: application/json');

try {
    // Check if the request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); // Method Not Allowed
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
        exit;
    }

    // Get the raw POST data
    $rawData = file_get_contents('php://input');
    $data = json_decode($rawData, true);

    // Validate the data
    if (!isset($data['supplies']) || !is_array($data['supplies'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'Invalid input data. Supplies must be an array.']);
        exit;
    }

    // Ensure notification_id is present
    $notificationId = $data['notification_id'] ?? null;
    if (!$notificationId) {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'Notification ID is missing.']);
        exit;
    }

    // Start a transaction to ensure data integrity
    $conn->begin_transaction();

    // Loop through each supply and deduct inventory
    foreach ($data['supplies'] as $supply) {
        if (!isset($supply['item_id'], $supply['quantity'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['status' => 'error', 'message' => 'Missing item_id or quantity for one or more supplies.']);
            $conn->rollback();
            exit;
        }

        $itemId = intval($supply['item_id']);
        $quantityToDeduct = intval($supply['quantity']);

        if ($quantityToDeduct <= 0) {
            http_response_code(400); // Bad Request
            echo json_encode(['status' => 'error', 'message' => 'Quantity to deduct must be greater than zero.']);
            $conn->rollback();
            exit;
        }

        // Check the current stock of the item
        $checkStmt = $conn->prepare('SELECT quantity FROM inventory WHERE id = ?');
        $checkStmt->bind_param('i', $itemId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows === 0) {
            http_response_code(404); // Not Found
            echo json_encode(['status' => 'error', 'message' => "Item ID $itemId not found in inventory."]);
            $conn->rollback();
            exit;
        }

        $item = $checkResult->fetch_assoc();
        if ($item['quantity'] < $quantityToDeduct) {
            http_response_code(400); // Bad Request
            echo json_encode(['status' => 'error', 'message' => "Insufficient stock for item ID $itemId. Current stock: " . $item['quantity']]);
            $conn->rollback();
            exit;
        }

        // Deduct the quantity from the inventory
        $updateStmt = $conn->prepare('UPDATE inventory SET quantity = quantity - ? WHERE id = ?');
        $updateStmt->bind_param('ii', $quantityToDeduct, $itemId);

        if (!$updateStmt->execute()) {
            http_response_code(500); // Internal Server Error
            echo json_encode(['status' => 'error', 'message' => 'Failed to update inventory for item ID ' . $itemId]);
            $conn->rollback();
            exit;
        }
    }

    // Update the status of the scheduled assistance to 'received'
    $updateStatusStmt = $conn->prepare('UPDATE scheduled_assistance SET status = "received" WHERE id = ?');
    $updateStatusStmt->bind_param('i', $notificationId); // Use notification_id (assumed to be the id in scheduled_assistance)
    
    if (!$updateStatusStmt->execute()) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['status' => 'error', 'message' => 'Failed to update assistance status.']);
        $conn->rollback();
        exit;
    }

    // Commit the transaction
    $conn->commit();

    // Respond with success
    echo json_encode(['status' => 'success', 'message' => 'Inventory updated and assistance status updated to "received".']);
} catch (Exception $e) {
    $conn->rollback(); // Roll back changes if an error occurs
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred.', 'details' => $e->getMessage()]);
} finally {
    $conn->close();
}
