<?php
// fetch_inventory.php

require 'db_connect.php'; // Include your database connection

$sql = "SELECT id, name, quantity, unit FROM inventory"; // Adjust this query as per your database structure
$result = $conn->query($sql);

$inventory = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $inventory[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'quantity' => $row['quantity'],
            'unit' => $row['unit']
        ];
    }
}

echo json_encode($inventory); // Return inventory items as JSON

$conn->close();
?>
