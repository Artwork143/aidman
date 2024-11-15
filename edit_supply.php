<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['edit_supply_id'];
    $name = $_POST['edit_supply_name'];
    $quantity = $_POST['edit_supply_quantity'];
    $expiry_date = $_POST['edit_expiry_date'];

    // Update database
    $sql = "UPDATE inventory SET name='$name', quantity='$quantity', expiry_date='$expiry_date' WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        header('Location: inventory-dashboard.php?status=edit');
    } else {
        header('Location: inventory-dashboard.php?status=error');
    }

    $conn->close();
}
?>
