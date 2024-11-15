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

// Ensure the 'id' is passed in the GET request
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('No user ID provided.'); window.location.href='account-management.php';</script>";
    exit();
}

$id = intval($_GET['id']); // Sanitize the 'id' value to avoid SQL injection

// Prepare and execute the query
$stmt = $conn->prepare("SELECT fullname, username, email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if user exists
if (!$user) {
    echo "<script>alert('User not found.'); window.location.href='account-management.php';</script>";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

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
            // Update user information in the database
            $updateStmt = $conn->prepare("UPDATE users SET fullname = ?, username = ?, email = ?, role = ? WHERE id = ?");
            $updateStmt->bind_param("ssssi", $fullname, $username, $email, $role, $id);

            if ($updateStmt->execute()) {
                // Check if a new password is provided
                if (!empty($_POST['new_password'])) {
                    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT); // Hash the new password
                    $passwordStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $passwordStmt->bind_param("si", $new_password, $id);
                    $passwordStmt->execute();
                }

                echo "<script>alert('User updated successfully!'); window.location.href='account-management.php';</script>";
            } else {
                echo "<script>alert('Error updating user. Please try again.'); window.history.back();</script>";
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
    <title>Edit User</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openModal() {
            document.getElementById('edit-user-modal-unique').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('edit-user-modal-unique').style.display = 'none';
        }

        function confirmEdit(event) {
            event.preventDefault();
            const adminUsername = document.getElementById('admin-username-unique').value;
            const adminPassword = document.getElementById('admin-password-unique').value;

            if (adminUsername === "" || adminPassword === "") {
                alert("Please enter admin username and password.");
                return false;
            }

            const form = document.getElementById('edit-user-form-unique');
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
<body id="edit-user-body-unique">
    <a href="account-management.php" id="edit-user-back-button-unique" class="back-button-unique">Back</a>
    <h2 id="edit-user-h2-unique">Edit User</h2>
    <form id="edit-user-form-unique" method="POST">
        <label for="fullname">Fullname:</label>
        <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
        
        <label for="username">Username:</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        
        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        
        <label for="role">Role:</label>
        <select name="role" required>
            <option value="resident" <?php echo $user['role'] == 'resident' ? 'selected' : ''; ?>>Resident</option>
            <option value="official" <?php echo $user['role'] == 'official' ? 'selected' : ''; ?>>Official</option>
            <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
        </select>

        <!-- New password field -->
        <label for="new_password">New Password (Optional):</label>
        <input type="password" name="new_password" placeholder="Enter new password">
        
        <button type="button" onclick="openModal()">Save</button>
    </form>

    <div id="edit-user-modal-unique" class="modal-unique">
        <div id="edit-user-modal-content-unique" class="modal-content-unique">
            <div id="edit-user-modal-header-unique" class="modal-header-unique">
                <h3>Admin Confirmation</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div id="edit-user-modal-body-unique" class="modal-body-unique">
                <label for="admin-username">Admin Username:</label>
                <input type="text" id="admin-username-unique" required placeholder="Enter admin username">
                <label for="admin-password">Admin Password:</label>
                <input type="password" id="admin-password-unique" required placeholder="Enter admin password">
            </div>
            <div id="edit-user-modal-footer-unique" class="modal-footer-unique">
                <button id="edit-user-confirm-button-unique" class="confirm-button-unique" onclick="confirmEdit(event)">Confirm</button>
                <button id="edit-user-cancel-button-unique" class="cancel-button-unique" onclick="closeModal()">Cancel</button>
            </div>
        </div>
    </div>
</body>
</html>
