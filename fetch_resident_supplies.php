<?php
require_once 'db_connect.php';

if (isset($_GET['resident_id'])) {
    $resident_id = intval($_GET['resident_id']);

    // Fetch resident supplies
    $query = "
        SELECT 
            i.id AS supply_id,
            i.name AS name,
            i.quantity AS available,
            i.unit AS unit,
            COALESCE(sai.quantity, 0) AS assigned_quantity
        FROM inventory i
        LEFT JOIN scheduled_assistance_items sai 
            ON i.id = sai.item_id
        LEFT JOIN scheduled_assistance sa 
            ON sai.schedule_id = sa.id
        WHERE sa.resident_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $resident_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $supplies = [];
    while ($row = $result->fetch_assoc()) {
        $supplies[] = [
            'id' => $row['supply_id'],
            'name' => $row['name'],
            'available' => $row['available'],
            'unit' => $row['unit'],
            'quantity' => $row['assigned_quantity']
        ];
    }

    echo json_encode(['supplies' => $supplies]);
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'No resident_id provided.']);
}
?>
