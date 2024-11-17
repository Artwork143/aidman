<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resident_id = $_POST['resident_id'];
    $pickup_date = $_POST['pickup_date'];
    $items = $_POST['items'];

    // Validate input
    if (empty($resident_id) || empty($pickup_date) || empty($items)) {
        die("Error: Missing required input data.");
    }

    // Create a notification message (optional, stored in the `scheduled_assistance` table)
    $notification_message = "Your assistance is scheduled for pickup on $pickup_date.";

    // Insert the schedule into `scheduled_assistance` table
    $stmt = $conn->prepare("INSERT INTO scheduled_assistance (resident_id, pickup_date, notification_message) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $resident_id, $pickup_date, $notification_message);
    if (!$stmt->execute()) {
        die("Error inserting schedule: " . $stmt->error);
    }
    $schedule_id = $stmt->insert_id;
    $stmt->close();

    // Insert the items into `scheduled_assistance_items` table
    foreach ($items as $item_id => $quantity) {
        if ($quantity > 0) {
            $stmt = $conn->prepare("INSERT INTO scheduled_assistance_items (schedule_id, item_id, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $schedule_id, $item_id, $quantity);
            if (!$stmt->execute()) {
                die("Error inserting schedule items: " . $stmt->error);
            }
            $stmt->close();
        }
    }

    echo "Schedule successfully created!";
    header("Location: assistance-scheduling.php");
    exit;
}

$conn->close();
