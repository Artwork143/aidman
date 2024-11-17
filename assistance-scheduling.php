<?php include 'check_admin.php';
require 'db_connect.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistance Scheduling</title>
    <link rel="stylesheet" href="assistance.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="css/admin-dashboard.css"> <!-- Updated link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="container" id="pageContent">
        <aside class="sidebar">
            <div class="logo">
                <img src="logo.jpg" alt="Barangay Logo">
                <h1>AIDMAN</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="aid-dashboard.php"><i class="fas fa-chart-line"></i> Aid Priority Ranking</a></li>
                    <li><a href="inventory-dashboard.php"><i class="fas fa-warehouse"></i> Inventory System</a></li>
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
                    <li class="nav-item active"><a href="assistance-scheduling.php"><i class="fas fa-calendar-check fa-lg mr-2"></i> Assistance Scheduling</a></li>
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
            <section class="main-content">
                <div class="table-container"> <!-- New container -->
                    <h3>List of Residents</h3>
                    <table border="1" cellspacing="0" cellpadding="5">
                        <thead>
                            <tr>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch users with the role of Resident
                            $sql = "SELECT id, fullname, email, username FROM users WHERE role = 'Resident'";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row['fullname'] . "</td>";
                                    echo "<td>" . $row['email'] . "</td>";
                                    echo "<td>Sent</td>";
                                    // Add data-resident-id attribute to pass the resident ID
                                    echo "<td><button class='btn-sched' data-resident-id='" . $row['id'] . "'>Schedule</button> <button class='btn-edit'>Edit</button> <button class='btn-delete'>Delete</button></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>No residents found.</td></tr>";
                            }

                            $conn->close();
                            ?>
                        </tbody>

                    </table>
                </div>
            </section>
        </main>
    </div>
    <!-- Modals -->
    <!-- Schedule Modal -->
    <div id="schedule-modal" class="modal">
        <div class="modal-content">
            <span class="close" id="close-schedule-modal">&times;</span>
            <h2>Schedule Assistance</h2>
            <form id="schedule-form" method="POST" action="schedule_assistance.php">
                <input type="hidden" name="resident_id" id="resident_id">

                <!-- Pickup Date -->
                <label for="pickup_date">Pickup Date:</label>
                <input type="date" name="pickup_date" required>

                <!-- Inventory Items and Quantities -->
                <h3>Supplies</h3>
                <?php
                // Fetch inventory items with their units
                require 'db_connect.php';
                $sql = "SELECT id, name, quantity, unit FROM inventory";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div>";
                        echo "<label for='item_" . $row['id'] . "'>" . $row['name'] . " (Available: " . $row['quantity'] . " " . $row['unit'] . ")</label>";
                        echo "<input type='number' id='item_" . $row['id'] . "' name='items[" . $row['id'] . "]' min='0' max='" . $row['quantity'] . "'>";
                        echo "<span>" . $row['unit'] . "</span>";
                        echo "</div>";
                    }
                }
                $conn->close();
                ?>

                <button type="submit" class="btn">Confirm Schedule</button>
                <button type="button" class="btn" id="cancel-schedule">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="edit-modal" class="modal">
        <div class="modal-content">
            <span class="close" id="close-edit-modal">&times;</span>
            <h2>Edit Resident</h2>
            <p>Are you sure you want to edit the details of this resident?</p>
            <button class="btn">Confirm</button>
            <button class="btn" id="cancel-edit">Cancel</button>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="delete-modal" class="modal">
        <div class="modal-content">
            <span class="close" id="close-delete-modal">&times;</span>
            <h2>Delete Resident</h2>
            <p>Are you sure you want to delete this resident?</p>
            <button class="btn">Confirm</button>
            <button class="btn" id="cancel-delete">Cancel</button>
        </div>
    </div>

    <script src="js/admin-dashboard.js"></script>
    <script>
        // Get modal elements
        const scheduleModal = document.getElementById('schedule-modal');
        const editModal = document.getElementById('edit-modal');
        const deleteModal = document.getElementById('delete-modal');

        // Get buttons for actions
        const schedButtons = document.querySelectorAll('.btn-sched');
        const editButtons = document.querySelectorAll('.btn-edit');
        const deleteButtons = document.querySelectorAll('.btn-delete');

        // Get close buttons
        const closeScheduleModal = document.getElementById('close-schedule-modal');
        const closeEditModal = document.getElementById('close-edit-modal');
        const closeDeleteModal = document.getElementById('close-delete-modal');
        const cancelSchedula = document.getElementById('cancel-schedule');

        // Add event listeners for opening modals
        schedButtons.forEach(button => {
            button.addEventListener('click', () => {
                scheduleModal.style.display = "block";
                document.getElementById('pageContent').classList.add('blur');
            });
        });

        const residentIdInput = document.getElementById('resident_id'); // Ensure there's an input with this ID in your form

        // Add event listeners for each Schedule button
        schedButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                // Get the resident ID from data attribute and set it in the form input
                const residentId = e.target.getAttribute('data-resident-id');
                residentIdInput.value = residentId;

                // Display the schedule modal
                scheduleModal.style.display = "block";
                document.getElementById('pageContent').classList.add('blur');
            });
        });

        editButtons.forEach(button => {
            button.addEventListener('click', () => {
                editModal.style.display = "block";
                document.getElementById('pageContent').classList.add('blur');
            });
        });

        deleteButtons.forEach(button => {
            button.addEventListener('click', () => {
                deleteModal.style.display = "block";
                document.getElementById('pageContent').classList.add('blur');
            });
        });

        // Add event listeners for closing modals
        closeScheduleModal.addEventListener('click', () => {
            scheduleModal.style.display = "none";
            document.getElementById('pageContent').classList.remove('blur');
        });

        closeEditModal.addEventListener('click', () => {
            editModal.style.display = "none";
            document.getElementById('pageContent').classList.remove('blur');
        });

        closeDeleteModal.addEventListener('click', () => {
            deleteModal.style.display = "none";
            document.getElementById('pageContent').classList.remove('blur');
        });

        cancelSchedula.addEventListener('click', () => {
            scheduleModal.style.display = "none";
            document.getElementById('pageContent').classList.remove('blur');
        });

        // Click outside the modal to close
        window.addEventListener('click', (event) => {
            if (event.target === scheduleModal) {
                scheduleModal.style.display = "none";
                document.getElementById('pageContent').classList.remove('blur');
            }
            if (event.target === editModal) {
                editModal.style.display = "none";
                document.getElementById('pageContent').classList.remove('blur');
            }
            if (event.target === deleteModal) {
                deleteModal.style.display = "none";
                document.getElementById('pageContent').classList.remove('blur');
            }
        });
    </script>
</body>
<!-- Logout Confirmation Modal -->
<div id="logout-modal" class="logout-modal">
    <div class="logout-content">
        <span class="close">&times;</span>
        <h2>Are you sure you want to logout?</h2>
        <button id="confirm-logout" class="btn">Yes</button>
        <button id="cancel-logout" class="btn">No</button>
    </div>
</div>

</html>