<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['supply_name'];
    $quantity = $_POST['supply_quantity'];
    $unit = $_POST['supply_unit'];
    $expiry_date = $_POST['expiry_date'];

    // Handling image upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["supply_image"]["name"]);
    move_uploaded_file($_FILES["supply_image"]["tmp_name"], $target_file);

    // Insert into database
    $sql = "INSERT INTO inventory (name, quantity, unit, expiry_date, image_path) VALUES ('$name', '$quantity', '$unit', '$expiry_date', '$target_file')";

    if ($conn->query($sql) === TRUE) {
        header('Location: inventory-dashboard.php?status=add');
    } else {
        header('Location: inventory-dashboard.php?status=error');
    }

    $conn->close();
}
?>
