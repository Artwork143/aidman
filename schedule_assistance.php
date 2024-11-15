<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $resident_id = $_POST['resident_id'];

    // Debugging: Check resident_id value before proceeding
    var_dump($resident_id);

    // Check if resident_id is null or empty
    if (empty($resident_id)) {
        die("Error: Resident ID is missing or invalid.");
    }
    $pickup_date = $_POST['pickup_date'];
    $items = $_POST['items'];

    // Insert schedule into `scheduled_assistance` table
    $stmt = $conn->prepare("INSERT INTO scheduled_assistance (resident_id, pickup_date) VALUES (?, ?)");
    $stmt->bind_param("is", $resident_id, $pickup_date);
    $stmt->execute();
    $schedule_id = $stmt->insert_id;
    $stmt->close();

    // Update `inventory` and add items to `scheduled_assistance_items`
    foreach ($items as $item_id => $quantity) {
        if ($quantity > 0) {
            // Subtract from inventory
            $stmt = $conn->prepare("UPDATE inventory SET quantity = quantity - ? WHERE id = ?");
            $stmt->bind_param("ii", $quantity, $item_id);
            $stmt->execute();
            $stmt->close();

            // Insert into `scheduled_assistance_items` (assuming this table exists)
            $stmt = $conn->prepare("INSERT INTO scheduled_assistance_items (schedule_id, item_id, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $schedule_id, $item_id, $quantity);
            $stmt->execute();
            $stmt->close();
        }
    }

    echo "Schedule successfully created!";
    header("Location: assistance-scheduling.php");
}
$conn->close();
