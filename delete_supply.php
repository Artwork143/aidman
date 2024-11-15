<?php
require 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete from database
    $sql = "DELETE FROM inventory WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        header('Location: inventory-dashboard.php?status=delete');
    } else {
        header('Location: inventory-dashboard.php?status=error');
    }

    $conn->close();
}
?>
