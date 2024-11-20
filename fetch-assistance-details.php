<?php
session_start(); // Start session at the beginning

// Include database connection
include 'db_connect.php';

header('Content-Type: application/json');

// Check if the connection is successful
if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit;
}

// Get the notification_id from the POST data (which refers to schedule_resident id)
$notification_id = isset($_POST['notification_id']) ? (int)$_POST['notification_id'] : null;

if (!$notification_id) {
    echo json_encode(['status' => 'error', 'message' => 'Notification ID is missing or invalid.']);
    exit;
}

// Log notification_id for debugging
error_log("Processing Notification ID: " . $notification_id);

// Query to find the corresponding scheduled_assistance entry using schedule_resident id
$sql = "
    SELECT sa.id AS notification_id
    FROM scheduled_assistance sa
    WHERE sa.resident_id = ?;
";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $notification_id); // Bind the schedule_resident id
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $scheduled_assistance_id = $row['notification_id']; // Get the actual scheduled_assistance id

        // Now fetch the details for the scheduled_assistance using the found id
        $details_sql = "
            SELECT 
                sa.id AS notification_id, 
                sa.notification_message, 
                sa.pickup_date, 
                sr.fullname AS resident_fullname,
                inv.id AS item_id, 
                inv.name AS item_name, 
                inv.unit AS item_unit, 
                sai.quantity 
            FROM 
                scheduled_assistance sa
            JOIN 
                schedule_residents sr ON sa.resident_id = sr.id
            JOIN 
                scheduled_assistance_items sai ON sa.id = sai.schedule_id
            JOIN 
                inventory inv ON sai.item_id = inv.id
            WHERE 
                sa.id = ?;
        ";

        if ($stmt = $conn->prepare($details_sql)) {
            $stmt->bind_param("i", $scheduled_assistance_id); // Use the found scheduled_assistance id
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $response = [
                    'status' => 'success',
                    'notification_id' => $scheduled_assistance_id,
                    'supplies' => []
                ];

                // Fetch the first row for resident details
                $row = $result->fetch_assoc();
                $response['notification_message'] = $row['notification_message'];
                $response['pickup_date'] = $row['pickup_date'];
                $response['resident_fullname'] = $row['resident_fullname'];

                // Add the first row's supply details
                $response['supplies'][] = [
                    'item_id' => $row['item_id'],
                    'item_name' => $row['item_name'],
                    'quantity' => $row['quantity'],
                    'unit' => $row['item_unit'],
                ];

                // Add additional supplies (if more rows exist)
                while ($row = $result->fetch_assoc()) {
                    $response['supplies'][] = [
                        'item_id' => $row['item_id'],
                        'item_name' => $row['item_name'],
                        'quantity' => $row['quantity'],
                        'unit' => $row['item_unit'],
                    ];
                }

                // Output the response as JSON
                echo json_encode($response);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No details found for the provided notification ID.']);
            }
        } else {
            // Log SQL preparation errors
            echo json_encode(['status' => 'error', 'message' => 'Failed to prepare the database query for details.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No scheduled assistance found for the provided resident.']);
    }
} else {
    // Log SQL preparation errors
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare the database query for notification ID.']);
}

// Close the database connection
$conn->close();
?>
