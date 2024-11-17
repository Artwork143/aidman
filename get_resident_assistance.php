<?php
require 'db_connect.php';

// Check if resident_id is provided
if (isset($_GET['resident_id'])) {
    $resident_id = intval($_GET['resident_id']);

    // Fetch the latest scheduled assistance for the resident
    $sql = "
        SELECT sa.id AS schedule_id, sa.pickup_date, sai.item_id, sai.quantity
        FROM scheduled_assistance sa
        LEFT JOIN scheduled_assistance_items sai ON sa.id = sai.schedule_id
        WHERE sa.resident_id = ?
        ORDER BY sa.id DESC
        LIMIT 4
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $resident_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $response = [
        'pickup_date' => '',
        'items' => []
    ];

    if ($result->num_rows > 0) {
        $assistanceData = $result->fetch_assoc();
        $response['pickup_date'] = $assistanceData['pickup_date'];

        // Fetch the items and their quantities
        do {
            $response['items'][$assistanceData['item_id']] = $assistanceData['quantity'];
        } while ($assistanceData = $result->fetch_assoc());
    }

    echo json_encode($response);
} else {
    echo json_encode(['error' => 'Missing resident_id parameter.']);
}

$conn->close();
