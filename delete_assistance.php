<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['resident_id']) && !empty($_POST['resident_id'])) {
        $resident_id = intval($_POST['resident_id']); // Sanitize input

        // Debugging: Log the resident ID
        error_log("Resident ID after processing: $resident_id");

        // Delete the assistance where status is 'for pickup'
        $sql = "DELETE FROM scheduled_assistance WHERE resident_id = ? AND status = 'for pickup'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $resident_id);

        if ($stmt->execute()) {
            header("Location: assistance-scheduling.php?success=deleted");
            exit;
        } else {
            echo "Error deleting record: " . $conn->error;
        }

        $stmt->close();
    } else {
        error_log("Invalid resident_id provided.");
        echo "Invalid resident ID.";
        error_log("Debugging info: resident_id (POST): " . print_r($_POST, true));

    }
} else {
    echo "Invalid request method.";
}
$conn->close();
?>
