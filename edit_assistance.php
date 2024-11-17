<?php
require 'db_connect.php';

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['resident_id'], $_POST['pickup_date'], $_POST['items'])) {
    $resident_id = intval($_POST['resident_id']);
    $pickup_date = $_POST['pickup_date'];
    $items = $_POST['items']; // An associative array: item_id => quantity

    // Validate inputs
    if (empty($pickup_date) || !is_array($items) || count($items) == 0) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Invalid input data.']);
        exit;
    }

    // Start a transaction to ensure data consistency
    $conn->begin_transaction();

    try {
        // 1. Update the scheduled_assistance table
        $stmt = $conn->prepare("UPDATE scheduled_assistance SET pickup_date = ? WHERE resident_id = ?");
        $stmt->bind_param("si", $pickup_date, $resident_id);
        $stmt->execute();
        
        // Get the schedule ID of the latest scheduled assistance for the resident
        $stmt = $conn->prepare("SELECT id FROM scheduled_assistance WHERE resident_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->bind_param("i", $resident_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $schedule_id = $result->fetch_assoc()['id'];

        // 2. Update the scheduled_assistance_items table (item_id => quantity)
        foreach ($items as $item_id => $quantity) {
            // Check if the item already exists for the given schedule ID, then update, otherwise insert
            $stmt = $conn->prepare("SELECT id FROM scheduled_assistance_items WHERE schedule_id = ? AND item_id = ?");
            $stmt->bind_param("ii", $schedule_id, $item_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Update existing item
                $stmt = $conn->prepare("UPDATE scheduled_assistance_items SET quantity = ? WHERE schedule_id = ? AND item_id = ?");
                $stmt->bind_param("iii", $quantity, $schedule_id, $item_id);
                $stmt->execute();
            } else {
                // Insert new item if it doesn't exist
                $stmt = $conn->prepare("INSERT INTO scheduled_assistance_items (schedule_id, item_id, quantity) VALUES (?, ?, ?)");
                $stmt->bind_param("iii", $schedule_id, $item_id, $quantity);
                $stmt->execute();
            }
        }

        // Commit the transaction
        $conn->commit();

        // Return success response
        echo json_encode(['success' => 'Assistance updated successfully.']);
    } catch (Exception $e) {
        // Rollback in case of error
        $conn->rollback();
        echo json_encode(['error' => 'Failed to update assistance: ' . $e->getMessage()]);
    }
} else {
    // If the required data is not provided
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Missing required parameters.']);
}

$conn->close();
?>
