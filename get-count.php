<?php
include 'db_connect.php';

header('Content-Type: application/json');

$response = array();

// Get total users
$sql = "SELECT COUNT(*) AS total_all FROM users";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response['total_all'] = $row['total_all'];
} else {
    $response['total_all'] = 0;
}

// Get total officials
$sql = "SELECT COUNT(*) AS total_officials FROM users WHERE role = 'Official'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response['total_officials'] = $row['total_officials'];
} else {
    $response['total_officials'] = 0;
}

// Get total residents
$sql = "SELECT COUNT(*) AS total_residents FROM users WHERE role = 'Resident'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response['total_residents'] = $row['total_residents'];
} else {
    $response['total_residents'] = 0;
}

echo json_encode($response);

$conn->close();
?>
