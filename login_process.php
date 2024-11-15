<?php
require 'db_connect.php'; // Ensure this file correctly connects to your database
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password, $role);

    if ($stmt->num_rows === 1) { // User exists
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) { // Password is correct
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;

            // Redirect to the appropriate dashboard
            if ($role === 'Admin') {
                header("Location: admin-dashboard.php");
            } elseif ($role === 'Official') {
                header("Location: official-dashboard.php");
            } else {
                header("Location: resident-dashboard.php");
            }
            exit(); // Ensure script stops after redirection
        } else {
            // Redirect with error for invalid password
            header("Location: login.php?error=invalid_password");
            exit();
        }
    } else {
        // Redirect with error for no user found
        header("Location: login.php?error=no_user");
        exit();
    }

    // Close statement and connection (not strictly necessary here as script ends)
    $stmt->close();
}
$conn->close();
?>
