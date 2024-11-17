<?php
session_start(); // Start session

// Include database connection
include 'db_connect.php';

header('Content-Type: application/json');

// Check if the connection is successful
if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit;
}

// Validate if `notification_id` is passed in the request
$notification_id = $_POST['notification_id'] ?? null;

if (!$notification_id) {
    echo json_encode(['status' => 'error', 'message' => 'Notification ID is missing or invalid.']);
    error_log("Missing Notification ID");
    exit;
}

// Log notification_id for debugging
error_log("Notification ID Received: " . $notification_id);

// Updated SQL query to include `item_id` from inventory table
$sql = "
    SELECT sa.id AS notification_id, 
           sa.notification_message, 
           sa.pickup_date, 
           sa.status AS assistance_status, 
           inv.id AS item_id, 
           inv.name AS item_name, 
           inv.unit AS item_unit, 
           sai.quantity 
    FROM scheduled_assistance sa
    JOIN scheduled_assistance_items sai ON sa.id = sai.schedule_id
    JOIN inventory inv ON sai.item_id = inv.id
    WHERE sa.id = ?
";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $notification_id); // Bind $notification_id
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $response = ['status' => 'success', 'supplies' => []];
        while ($row = $result->fetch_assoc()) {
            $response['notification_message'] = $row['notification_message'];
            $response['pickup_date'] = $row['pickup_date'];
            $response['assistance_status'] = $row['assistance_status']; // Added status of assistance
            $response['supplies'][] = [
                'item_id' => $row['item_id'], // Item ID included
                'item_name' => $row['item_name'],
                'quantity' => $row['quantity'],
                'unit' => $row['item_unit'],
            ];
        }
        echo json_encode($response); // Return successful response
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No details found for the provided notification ID.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare the database query.']);
    error_log("SQL Prepare Error: " . $conn->error);
}

$conn->close(); // Close database connection
