<?php
header('Content-Type: application/json'); // Specify JSON response type

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_connect.php'; // Include your database connection

// Validate the query parameter
if (!isset($_GET['q']) || strlen(trim($_GET['q'])) < 3) {
    echo json_encode(['error' => 'Search query must be at least 3 characters long.']);
    exit;
}

$query = $conn->real_escape_string($_GET['q']); // Sanitize user input

// Check if the fullname already exists in schedule_residents
$checkSql = "SELECT fullname FROM schedule_residents WHERE fullname LIKE '%$query%' LIMIT 1";
$checkResult = $conn->query($checkSql);

if ($checkResult && $checkResult->num_rows > 0) {
    $existingResident = $checkResult->fetch_assoc();
    echo json_encode([
        'error' => 'Resident already exists in scheduled residents.',
        'fullname' => $existingResident['fullname']
    ]);
    exit;
}

// Prepare and execute the SQL query for searching in users
$sql = "
    SELECT id, fullname 
    FROM users
    WHERE fullname LIKE '%$query%' 
    LIMIT 10
";
$result = $conn->query($sql);

if (!$result) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Database query failed.']);
    exit;
}

// Fetch results and output as JSON
$residents = [];
while ($row = $result->fetch_assoc()) {
    $residents[] = [
        'id' => $row['id'],
        'fullname' => $row['fullname']
    ];
}

// If no results were found, return a "not found" message
if (empty($residents)) {
    echo json_encode(['error' => 'No residents found in the database matching the search query.']);
} else {
    echo json_encode($residents); // Return the matching residents
}

$conn->close();
?>
