<?php include 'check_admin.php'; ?>

<?php
// Database Connection (db_connect.php)
$servername = "localhost";
$username = "root";
$password = "";
$database = "aidman-db";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="css/admin-dashboard.css"> <!-- Updated link -->
    <link rel="stylesheet" href="css/inventory-modal.css"> <!-- Added inventory modal CSS link -->
    <!-- Link to Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <img src="logo.jpg" alt="Barangay Logo">
                <h1>AIDMAN</h1>
                <p></p>
            </div>
            <nav>
                <ul>
                    <li ><a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i></i> Dashboard</a></li>
                    <li ><a href="aid-dashboard.php"><i class="fas fa-chart-line"></i> Aid Priority Ranking</a></li>
                    <li class="nav-item active"><a href="inventory-dashboard.php"><i class="fas fa-warehouse"></i> Inventory System</a></li>
                    <li class="arrow-dropdown">
                        <div class="arrow-dropdown-toggle" id="account-control-link">
                            <a href="account_control.php" style="flex-grow: 1;"><i class="fas fa-user-cog mr-2"></i> Account Control Panel Register</a>
                            <i class="fas fa-chevron-down arrow-toggle"></i>
                        </div>
                        <div class="arrow-dropdown-content">
                            <a href="account-management.php"><i class="fa-solid fa-file-invoice"></i> Account Management</a>
                        </div>
                    </li>
                    <li><a href="event-control-system.php"><i class="fas fa-calendar-alt fa-lg mr-2"></i> Event Control System</a></li>
                    <li><a href="assistance-scheduling.php"><i class="fas fa-calendar-check fa-lg mr-2"></i> Assistance Scheduling</a></li>
                </ul>
            </nav>
        </aside>
        <main>
            <header>
                <h2>Administrator</h2>
                <div class="header-right">
                    <i class="fas fa-bell"></i>
                    <div class="dropdown">
                        <i class="fas fa-user-circle"></i>
                        <div class="dropdown-menu">
                            <a href="account-information.php" id="view-profile" class="dropdown-item">
                                <i class="fas fa-user"></i>
                                <span>Account Info</span>
                            </a>
                            <a href="./email-inbox.html" class="dropdown-item">
                                <i class="fas fa-envelope-open"></i>
                                <span>Inbox</span>
                            </a>
                            <a href="login.php" id="logout-link" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>                            
                        </div>
                    </div>
                </div>
            </header>

            <div class="inventory-container">
                <button id="inven-add-supplies-btn" class="inven-btn inven-center-page">Add Supplies</button>
                <div class="inventory-list" id="inven-inventory-list">
                <?php
                // Fetch inventory items from the database
                require 'db_connect.php';
                $sql = "SELECT * FROM inventory";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="inventory-item">';
                        echo '<div class="inventory-card">';
                        if ($row['image_path']) {
                            echo '<img src="' . $row['image_path'] . '" alt="' . $row['name'] . '" class="inventory-image">';
                        }
                        echo '<div class="inventory-details">';
                        echo '<h4 class="inventory-name">' . $row['name'] . '</h4>';
                        echo '<p class="inventory-info" data-expiry-date="' . $row['expiry_date'] . '"><strong>Quantity:</strong> ' . $row['quantity'] . ' ' . $row['unit'] . '</p>';
                        echo '<div class="inventory-actions">';
                        echo '<button class="edit-supply-btn" data-id="' . $row['id'] . '">Edit</button>';
                        echo '<button class="delete-supply-btn" data-id="' . $row['id'] . '">Delete</button>';
                        echo '</div>'; // .inventory-actions
                        echo '</div>'; // .inventory-details
                        echo '</div>'; // .inventory-card
                        echo '</div>'; // .inventory-item
                    }
                }
                ?>
                </div>
            </div>

            <!-- Add Supplies Modal -->
            <div id="inven-add-supplies-modal" class="inven-modal">
                <div class="inven-modal-content inven-realistic-modal">
                    <span class="inven-close">&times;</span>
                    <h2>Add Supplies</h2>
                    <form id="inven-add-supplies-form" action="add_supply.php" method="POST" enctype="multipart/form-data">
                        <label for="inven-supply-name">Name:</label>
                        <input type="text" id="inven-supply-name" name="supply_name" required>

                        <label for="inven-supply-quantity">Quantity:</label>
                        <input type="number" id="inven-supply-quantity" name="supply_quantity" required>

                        <label for="inven-supply-unit">Unit:</label>
                        <select id="inven-supply-unit" name="supply_unit">
                            <option value="kg">Kilo</option>
                            <option value="amount">Amount</option>
                        </select>

                        <label for="inven-expiry-date">Expiry Date:</label>
                        <input type="date" id="inven-expiry-date" name="expiry_date" required>

                        <label for="inven-supply-image">Upload Image:</label>
                        <input type="file" id="inven-supply-image" name="supply_image" accept="image/*">

                        <button type="submit" class="inven-btn">Save</button>
                    </form>
                </div>
            </div>

            <!-- Edit Supplies Modal -->
            <div id="inven-edit-supplies-modal" class="inven-modal">
                <div class="inven-modal-content inven-realistic-modal">
                    <span class="inven-close-edit">&times;</span>
                    <h2>Edit Supplies</h2>
                    <form id="inven-edit-supplies-form" action="edit_supply.php" method="POST">
                        <input type="hidden" id="edit-supply-id" name="edit_supply_id">
                        <label for="edit-supply-name">Name:</label>
                        <input type="text" id="edit-supply-name" name="edit_supply_name" required>

                        <label for="edit-supply-quantity">Quantity:</label>
                        <input type="number" id="edit-supply-quantity" name="edit_supply_quantity" required>

                        <label for="edit-expiry-date">Expiry Date:</label>
                        <input type="date" id="edit-expiry-date" name="edit_expiry_date" required>

                        <button type="submit" class="inven-btn"> Update</button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- Delete Confirmation Modal -->
<div id="inven-delete-supply">
    <div class="inven-delete-content">
        <span class="inven-close-delete">&times;</span>
        <h2>Delete Supply</h2>
        <p>Are you sure you want to delete this supply?</p>
        <form id="delete-supply-form" action="delete_supply.php" method="GET">
            <input type="hidden" id="delete-supply-id" name="id">
            <button type="submit" class="delete-confirm-btn">Yes, Delete</button>
            <button type="button" class="delete-cancel-btn"  class="delete-cancel-btn">Cancel</button>
        </form>
    </div>
</div>

    
    <script src="js/admin-dashboard.js"></script>
    <script>
        // Modal Script
        const addSuppliesBtn = document.getElementById('inven-add-supplies-btn');
        const addSuppliesModal = document.getElementById('inven-add-supplies-modal');
        const closeModal = document.querySelector('.inven-close');
        const addSuppliesForm = document.getElementById('inven-add-supplies-form');
        const inventoryList = document.getElementById('inven-inventory-list');

        const editSuppliesModal = document.getElementById('inven-edit-supplies-modal');
        const closeModalEdit = document.querySelector('.inven-close-edit');
        const editSuppliesForm = document.getElementById('inven-edit-supplies-form');

        addSuppliesBtn.addEventListener('click', () => {
            addSuppliesModal.style.display = 'block';
        });

        closeModal.addEventListener('click', () => {
            addSuppliesModal.style.display = 'none';
        });

        closeModalEdit.addEventListener('click', () => {
            editSuppliesModal.style.display = 'none';
        });

        window.addEventListener('click', (event) => {
            if (event.target == addSuppliesModal) {
                addSuppliesModal.style.display = 'none';
            }
            if (event.target == editSuppliesModal) {
                editSuppliesModal.style.display = 'none';
            }
        });

        // Form submission handler for adding supplies
        addSuppliesForm.addEventListener('submit', (event) => {
            event.preventDefault(); // Prevent form from submitting normally

            const formData = new FormData(addSuppliesForm);

            fetch('add_supply.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                Swal.fire('Success', 'Supply added successfully!', 'success').then(() => {
                    window.location.reload();
                });
            })
            .catch(error => {
                Swal.fire('Error', 'An error occurred. Please try again.', 'error');
            });
        });

        // Event listener for handling edit and delete functionality
        document.addEventListener('click', (event) => {
            if (event.target.classList.contains('edit-supply-btn')) {
                const supplyId = event.target.getAttribute('data-id');
                const supplyName = event.target.closest('.inventory-item').querySelector('.inventory-name').textContent;
                const supplyQuantity = event.target.closest('.inventory-item').querySelector('.inventory-info').textContent.match(/\d+/)[0];
                const expiryDate = event.target.closest('.inventory-item').querySelector('.inventory-info').getAttribute('data-expiry-date');

                let formattedDate = "";
                if (expiryDate && expiryDate !== '0000-00-00') {
                    formattedDate = expiryDate;  
                }

                document.getElementById('edit-supply-id').value = supplyId;        
                document.getElementById('edit-supply-name').value = supplyName;     
                document.getElementById('edit-supply-quantity').value = supplyQuantity; 
                document.getElementById('edit-expiry-date').value = formattedDate;  

                // Show the modal for editing supplies
                document.getElementById('inven-edit-supplies-modal').style.display = 'block';
            }

            // Check if the clicked element has the class 'delete-btn' (Delete button)
            if (event.target.classList.contains('delete-supply-btn')) {
                // Fetch supply ID from the data-id attribute of the clicked delete button
                const supplyId = event.target.getAttribute('data-id');
                
                // Sweet Alert confirmation before deletion
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `delete_supply.php?id=${supplyId}`;
                    }
                });
            }
        });

        // Form submission handler for editing supplies
        editSuppliesForm.addEventListener('submit', (event) => {
            event.preventDefault();
            const formData = new FormData(editSuppliesForm);

            fetch('edit_supply.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                Swal.fire('Success', 'Supply updated successfully!', 'success').then(() => {
                    window.location.reload();
                });
            })
            .catch(error => {
                Swal.fire('Error', 'An error occurred. Please try again.', 'error');
            });
        });
    </script>
</body>
<!-- Logout Confirmation Modal -->
<div id="logout-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Are you sure you want to logout?</h2>
        <button id="confirm-logout" class="btn">Yes</button>
        <button id="cancel-logout" class="btn">No</button>
    </div>
</html>

<?php
// add_supply.php
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
        echo "success";
    } else {
        echo "error";
    }

    $conn->close();
}
?>

<?php
// edit_supply.php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['edit_supply_id'];
    $name = $_POST['edit_supply_name'];
    $quantity = $_POST['edit_supply_quantity'];
    $expiry_date = $_POST['edit_expiry_date'];

    // Update database
    $sql = "UPDATE inventory SET name='$name', quantity='$quantity', expiry_date='$expiry_date' WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "error";
    }

    $conn->close();
}
?>

<?php
// delete_supply.php
require 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete from database
    $sql = "DELETE FROM inventory WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>window.location.href = 'admin-dashboard.php?status=delete';</script>";
    } else {
        echo "<script>window.location.href = 'admin-dashboard.php?status=error';</script>";
    }

    $conn->close();
}
?>