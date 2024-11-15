<?php
// Database connection details
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

// Get user ID from the GET request
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT fullname, username, email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Admin credentials from the form
    $admin_username = $_POST['admin_username'];
    $admin_password = $_POST['admin_password'];

    // Fetch admin credentials from the database
    $adminStmt = $conn->prepare("SELECT password FROM users WHERE username = ? AND role = 'admin'");
    $adminStmt->bind_param("s", $admin_username);
    $adminStmt->execute();
    $adminResult = $adminStmt->get_result();

    if ($adminResult->num_rows > 0) {
        $admin = $adminResult->fetch_assoc();

        // Verify the admin's password
        if (password_verify($admin_password, $admin['password'])) {
            // Delete user from the database
            $deleteStmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $deleteStmt->bind_param("i", $id);

            if ($deleteStmt->execute()) {
                echo "<script>alert('User deleted successfully!'); window.location.href='account-management.php';</script>";
            } else {
                echo "<script>alert('Error deleting user. Please try again.'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Invalid admin password.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Admin username not found.'); window.history.back();</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete User</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openModal() {
            document.getElementById('delete-manage-modal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('delete-manage-modal').style.display = 'none';
        }

        function confirmDelete(event) {
            event.preventDefault();
            const adminUsername = document.getElementById('delete-manage-admin-username').value;
            const adminPassword = document.getElementById('delete-manage-admin-password').value;

            if (adminUsername === "" || adminPassword === "") {
                alert("Please enter admin username and password.");
                return false;
            }

            const form = document.getElementById('delete-manage-form');
            const usernameInput = document.createElement('input');
            usernameInput.type = 'hidden';
            usernameInput.name = 'admin_username';
            usernameInput.value = adminUsername;
            form.appendChild(usernameInput);

            const passwordInput = document.createElement('input');
            passwordInput.type = 'hidden';
            passwordInput.name = 'admin_password';
            passwordInput.value = adminPassword;
            form.appendChild(passwordInput);

            form.submit();
        }
    </script>
</head>
<body id="delete-manage-body">
    <h2 id="delete-manage-h2">Delete User</h2>
    <form id="delete-manage-form" method="POST">
        <p>Are you sure you want to delete the user <strong><?php echo htmlspecialchars($user['fullname']); ?></strong>?</p>
        <button type="button" id="delete-manage-button" onclick="openModal()">Delete User</button>
    </form>

    <div id="delete-manage-modal" class="modal-delete-manage">
        <div id="delete-manage-modal-content" class="modal-content-delete-manage">
            <div id="delete-manage-modal-header" class="modal-header-delete-manage">
                <h3>Admin Confirmation</h3>
                <span class="close-delete-manage" onclick="closeModal()">&times;</span>
            </div>
            <div id="delete-manage-modal-body" class="modal-body-delete-manage">
                <label for="admin-username">Admin Username:</label>
                <input type="text" id="delete-manage-admin-username" required placeholder="Enter admin username">
                <label for="admin-password">Admin Password:</label>
                <input type="password" id="delete-manage-admin-password" required placeholder="Enter admin password">
            </div>
            <div id="delete-manage-modal-footer" class="modal-footer-delete-manage">
                <button class="confirm-button-delete-manage" onclick="confirmDelete(event)">Confirm</button>
                <button class="cancel-button-delete-manage" onclick="closeModal()">Cancel</button>
            </div>
        </div>
    </div>
</body>
</html>
