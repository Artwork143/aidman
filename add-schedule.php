<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get posted data
    $resident_id = $_POST['resident_id'];
    $fullname = $_POST['fullname'];  // Full name of the resident from the search modal
    $distribution_date = $_POST['distribution_date'];
    $status = $_POST['status'];  // status from the schedule modal
    $items = json_decode($_POST['items'], true); // Decode items JSON

    // Check if required fields are provided
    if (empty($resident_id) || empty($fullname) || empty($distribution_date) || empty($status)) {
        echo json_encode(['error' => 'Missing required fields.']);
        exit;
    }

    // Start database transaction
    $conn->begin_transaction();

    try {
        // Check if the resident exists in the `schedule_residents` table
        $resident_check_query = "SELECT id FROM schedule_residents WHERE id = ?";
        $stmt = $conn->prepare($resident_check_query);
        $stmt->bind_param("i", $resident_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // If the resident does not exist in the `schedule_residents` table, insert the resident
        if ($result->num_rows === 0) {
            $insert_resident_query = "INSERT INTO schedule_residents (id, fullname, assistance_status) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_resident_query);
            $stmt->bind_param("iss", $resident_id, $fullname, $status);
            $stmt->execute();
            $stmt->close();
        } else {
            // If the resident exists, update their status
            $update_status_query = "UPDATE schedule_residents SET assistance_status = ? WHERE id = ?";
            $stmt = $conn->prepare($update_status_query);
            $stmt->bind_param("si", $status, $resident_id);
            $stmt->execute();
            $stmt->close();
        }

        // Insert schedule into `scheduled_assistance`
        $stmt = $conn->prepare("
            INSERT INTO scheduled_assistance (resident_id, pickup_date, notification_message) 
            VALUES (?, ?, ?)
        ");
        $notification_message = "Scheduled for pickup on " . $distribution_date;
        $stmt->bind_param("iss", $resident_id, $distribution_date, $notification_message);
        $stmt->execute();
        $schedule_id = $stmt->insert_id; // Get the inserted schedule ID
        $stmt->close();

        // Insert items into `scheduled_assistance_items`
        foreach ($items as $item_id => $quantity) {
            $stmt = $conn->prepare("
                INSERT INTO scheduled_assistance_items (schedule_id, item_id, quantity)
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("iii", $schedule_id, $item_id, $quantity);
            $stmt->execute();
            $stmt->close();

            // Deduct inventory only if status is "received"
            if ($status === 'received') {
                $stmt = $conn->prepare("
                    UPDATE inventory
                    SET quantity = quantity - ?
                    WHERE id = ?
                ");
                $stmt->bind_param("ii", $quantity, $item_id);
                $stmt->execute();
                $stmt->close();
            }
        }

        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['error' => 'Failed to save schedule: ' . $e->getMessage()]);
    }

    $conn->close();
}
