<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Parse the JSON body
    $input = json_decode(file_get_contents('php://input'), true);
    $resident_id = $input['resident_id'] ?? null;

    if (empty($resident_id)) {
        echo json_encode(['error' => 'Missing required resident ID.']);
        exit;
    }

    $conn->begin_transaction();

    try {
        // Delete items linked to the resident's schedule
        $stmt = $conn->prepare("
            DELETE FROM scheduled_assistance_items 
            WHERE schedule_id = (SELECT id FROM scheduled_assistance WHERE resident_id = ?)
        ");
        $stmt->bind_param("i", $resident_id);
        $stmt->execute();
        $stmt->close();

        // Delete the schedule
        $stmt = $conn->prepare("DELETE FROM scheduled_assistance WHERE resident_id = ?");
        $stmt->bind_param("i", $resident_id);
        $stmt->execute();
        $stmt->close();

        // Optionally delete the resident from the `schedule_residents` table
        $stmt = $conn->prepare("DELETE FROM schedule_residents WHERE id = ?");
        $stmt->bind_param("i", $resident_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['error' => 'Failed to delete schedule: ' . $e->getMessage()]);
    }

    $conn->close();
}
?>
