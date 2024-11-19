<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resident_id = $_POST['resident_id'] ?? null; // Fetch resident_id safely
    $distribution_date = $_POST['distribution_date'] ?? null;
    $status = $_POST['status'] ?? null;
    $items = json_decode($_POST['items'] ?? "[]", true); // Default to empty array if not provided

    // Validate required fields
    if (empty($resident_id) || empty($distribution_date) || empty($status)) {
        echo json_encode(['error' => 'Missing required fields.']);
        exit;
    }

    $conn->begin_transaction();

    try {
        // Update resident status
        $stmt = $conn->prepare("UPDATE schedule_residents SET assistance_status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $resident_id);
        $stmt->execute();
        $stmt->close();

        // Update schedule
        $stmt = $conn->prepare("
            UPDATE scheduled_assistance 
            SET pickup_date = ?, notification_message = ? 
            WHERE resident_id = ?
        ");
        $notification_message = "Scheduled for pickup on " . $distribution_date;
        $stmt->bind_param("ssi", $distribution_date, $notification_message, $resident_id);
        $stmt->execute();
        $stmt->close();

        // Clear existing items for the resident
        $stmt = $conn->prepare("DELETE FROM scheduled_assistance_items WHERE schedule_id = 
            (SELECT id FROM scheduled_assistance WHERE resident_id = ?)");
        $stmt->bind_param("i", $resident_id);
        $stmt->execute();
        $stmt->close();

        // Insert updated items
        foreach ($items as $item_id => $quantity) {
            $stmt = $conn->prepare("
                INSERT INTO scheduled_assistance_items (schedule_id, item_id, quantity) 
                VALUES ((SELECT id FROM scheduled_assistance WHERE resident_id = ?), ?, ?)
            ");
            $stmt->bind_param("iii", $resident_id, $item_id, $quantity);
            $stmt->execute();
            $stmt->close();

            // Update inventory only if status is "received"
            if ($status === "received") {
                $stmt = $conn->prepare("UPDATE inventory SET quantity = quantity - ? WHERE id = ?");
                $stmt->bind_param("ii", $quantity, $item_id);
                $stmt->execute();
                $stmt->close();
            }
        }

        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['error' => 'Failed to update schedule: ' . $e->getMessage()]);
    }

    $conn->close();
}
?>
