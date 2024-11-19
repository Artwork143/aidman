<?php
header('Content-Type: application/json');

require_once 'db_connect.php';

// Get resident ID from POST request
$data = json_decode(file_get_contents('php://input'), true);
$residentId = isset($data['residentId']) ? $conn->real_escape_string($data['residentId']) : '';

if (empty($residentId)) {
    http_response_code(400);
    echo json_encode(['error' => 'Resident ID is required']);
    exit;
}

// Check if resident exists in the 'residents' table (assuming this table exists)
$sql = "SELECT id, fullname FROM users WHERE id = '$residentId' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Resident not found']);
    exit;
}

// Fetch resident details
$resident = $result->fetch_assoc();
$fullname = $resident['fullname'];

// Insert resident into 'schedule_residents' table
$insertSql = "INSERT INTO schedule_residents (fullname, created_at, updated_at) 
              VALUES ('$fullname', NOW(), NOW())";

if ($conn->query($insertSql) === TRUE) {
    // Respond with the inserted record
    $scheduledResidentId = $conn->insert_id;
    echo json_encode([
        'id' => $scheduledResidentId,
        'fullname' => $fullname,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to add resident to schedule.']);
}

$conn->close();
?>
