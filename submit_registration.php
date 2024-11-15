<?php
// Connect to the database
$servername = "localhost";
$username = "root"; // your MySQL username
$password = ""; // your MySQL password
$dbname = "aidman-db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$fullname = $_POST['fullname'];
$email = $_POST['email'];
$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role = $_POST['role']; // This should be 'Resident'

// Ensure role is 'Resident'
if ($role !== 'Resident') {
    die("Invalid role.");
}

// Check for duplicate email, username, or fullname
$stmt = $conn->prepare("SELECT email, username, fullname FROM users WHERE email = ? OR username = ? OR fullname = ?");
$stmt->bind_param("sss", $email, $username, $fullname);
$stmt->execute();
$stmt->bind_result($existing_email, $existing_username, $existing_fullname);
$stmt->fetch();
$stmt->close();

$duplicate = '';
if ($existing_email == $email) {
    $duplicate = 'email';
} elseif ($existing_username == $username) {
    $duplicate = 'username';
} elseif ($existing_fullname == $fullname) {
    $duplicate = 'fullname';
}

if ($duplicate) {
    // Redirect with duplicate error
    header("Location: register-complete.php?status=failed&reason=duplicate_$duplicate&fullname=" . urlencode($fullname) . "&email=" . urlencode($email) . "&username=" . urlencode($username));
    exit();
}

// Prepare SQL statement to prevent SQL injection
$stmt = $conn->prepare("INSERT INTO users (fullname, email, username, password, role) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $fullname, $email, $username, $password, $role);

// Execute the statement
if ($stmt->execute()) {
    // Registration successful, redirect to control-complete.php
    header("Location: register-complete.php?status=success");
    exit(); // Make sure no further code is executed
} else {
    // Handle other errors
    header("Location: reg-forum.php?status=failed&reason=error&fullname=" . urlencode($fullname) . "&email=" . urlencode($email) . "&username=" . urlencode($username));
    exit();
}

// Close connection
$stmt->close();
$conn->close();
?>
