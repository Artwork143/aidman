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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .notification-bell {
            position: relative;
            cursor: pointer;
        }

        .notification-bell .badge {
            position: absolute;
            top: -10px;
            right: -12px;
            height: 7px;
            width: 7px;
            background-color: red;
            color: white;
            border-radius: 50%;
            border: 2px solid white;
            padding: 5px;
            font-size: 12px;
            padding-bottom: 10px;
        }

        .dropdown-menu.notifications {
            display: none;
            position: absolute;
            right: 0;
            top: 30px;
            background-color: white;
            border: 1px solid #ddd;
            width: 300px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }

        .dropdown-menu.notifications.show {
            display: block;
        }

        .dropdown-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .dropdown-item:hover {
            background-color: #f1f1f1;
        }

        .hidden {
            display: none;
        }

        .modal-search {

            /* Hidden by default */
            position: fixed;
            z-index: 1;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
            /* Black w/ opacity */
        }

        .modal-search-content {
            display: flex;
            flex-direction: column;
            gap: 10px;

            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;

            padding: 20px;
            width: 400px;
            border-radius: 8px;
        }

        .modal-close {
            align-self: flex-end;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
        }

        #residentSearch {
            padding: 8px;
            width: 100%;
            box-sizing: border-box;
        }

        #residentSearchResults {
            max-height: 200px;
            overflow-y: auto;
            margin: 10px 0;
            border: 1px solid #ccc;
            padding: 10px;
        }

        #residentSearchResults div {
            padding: 5px;
            cursor: pointer;
        }

        #residentSearchResults div:hover {
            background: #f0f0f0;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
        }

        .btn-add {
            margin-right: 10px;
            padding: 5px 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .btn-add:hover {
            background: #0056b3;
        }
    </style>
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
                    <!-- Notification Bell -->
                    <div class="notification-bell" id="notification-bell">
                        <?php
                        // Fetch low-stock inventory items
                        require 'db_connect.php';
                        $sql = "SELECT name, quantity, threshold_quantity FROM inventory WHERE quantity <= threshold_quantity";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        $lowStockNotifications = [];
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $lowStockNotifications[] = [
                                    'name' => $row['name'],
                                    'quantity' => $row['quantity'],
                                    'threshold' => $row['threshold_quantity'],
                                ];
                            }
                        }

                        $hasLowStock = !empty($lowStockNotifications);
                        ?>
                        <i class="fas fa-bell" style="color: <?php echo $hasLowStock ? 'red' : '#555'; ?>;"></i>
                        <?php if ($hasLowStock): ?>
                            <span class="badge"><?php echo count($lowStockNotifications); ?></span>
                        <?php endif; ?>
                        <div class="dropdown-menu notifications" id="notification-dropdown">
                            <?php if ($hasLowStock): ?>
                                <?php foreach ($lowStockNotifications as $notification): ?>
                                    <div class="dropdown-item" onclick="location.href='inventory-dashboard.php';" style="cursor: pointer;">
                                        <p>
                                            <strong>Low Stock Alert:</strong> <?php echo htmlspecialchars($notification['name']); ?><br>
                                            Current Quantity: <?php echo htmlspecialchars($notification['quantity']); ?><br>
                                            Restock Threshold: <?php echo htmlspecialchars($notification['threshold']); ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="dropdown-item">
                                    <p>No low-stock alerts</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="profile-dropdown" id="profile-dropdown">
                        <i class="fas fa-user-circle"></i>
                        <div class="dropdown-menu profile-menu" id="profile-menu">
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
                <div class="table-container">
                    <div class="table-header">
                        <h3>Scheduled Residents</h3>
                        <button id="addResidentBtn" class="btn-add">Add Resident</button>
                    </div>
                    <table border="1" cellspacing="0" cellpadding="5">
                        <thead>
                            <tr>
                                <th>Full Name</th>
                                <th>Distribution Date/Time</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="residentTableBody">
                            <?php
                            require_once 'db_connect.php';

                            // Fetch data from schedule_residents with the latest schedule details
                            $sql = "
                    SELECT 
                        sr.id,
                        sr.fullname,
                        sa.pickup_date AS distribution_datetime,
                        COALESCE(
                            CASE
                                WHEN sr.assistance_status = 'for pickup' THEN 'For Pickup'
                                WHEN sr.assistance_status = 'received' THEN 'Received'
                                ELSE 'Eligible'
                            END,
                            'Eligible'
                        ) AS assistance_status
                    FROM schedule_residents sr
                    LEFT JOIN (
                        SELECT id, resident_id, pickup_date
                        FROM scheduled_assistance
                        WHERE id IN (
                            SELECT MAX(id) 
                            FROM scheduled_assistance 
                            GROUP BY resident_id
                        )
                    ) sa ON sr.id = sa.resident_id
                ";

                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['fullname']) . "</td>";
                                    echo "<td>" . (!empty($row['distribution_datetime']) ? htmlspecialchars($row['distribution_datetime']) : 'N/A') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['assistance_status']) . "</td>";
                                    echo "<td>";
                                    if ($row['assistance_status'] !== 'For Pickup') {
                                        echo "<button class='btn-sched' data-resident-id='" . htmlspecialchars($row['id']) . "'>Schedule</button>";
                                    }
                                    if ($row['assistance_status'] === 'For Pickup') {
                                        echo "<button class='btn-edit' data-resident-id='" . htmlspecialchars($row['id']) . "'>Edit</button>";
                                        
                                    }
                                    echo "<button class='btn-delete' data-resident-id='" . htmlspecialchars($row['id']) . "'>Delete</button>";
                                    echo "</td>";
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

    <!-- Modal for Editing Assistance -->
    <div id="edit-modal" class="modal">
        <div class="modal-content">
            <span class="close" id="close-edit-modal">&times;</span>
            <h2>Edit Assistance</h2>
            <form id="edit-form" method="POST" action="edit_assistance.php">
                <input type="hidden" name="resident_id" id="edit-resident-id">

                <!-- Pickup Date -->
                <label for="edit-pickup-date">Pickup Date:</label>
                <input type="date" name="pickup_date" id="edit-pickup-date" required>

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
                        echo "<label for='edit-item_" . $row['id'] . "'>" . $row['name'] . " (Available: " . $row['quantity'] . " " . $row['unit'] . ")</label>";
                        echo "<input type='number' id='edit-item_" . $row['id'] . "' name='items[" . $row['id'] . "]' min='0' max='" . $row['quantity'] . "'>";
                        echo "<span>" . $row['unit'] . "</span>";
                        echo "</div>";
                    }
                }
                $conn->close();
                ?>

                <button type="submit" class="btn">Update</button>
                <button type="button" class="btn" id="cancel-edit">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="delete-modal" class="modal">
        <div class="modal-content">
            <span class="close" id="close-delete-modal">&times;</span>
            <h2>Delete Schedule</h2>
            <p>Are you sure you want to delete this schedule?</p>
            <form id="delete-form" method="POST" action="delete_assistance.php" style="display: inline-block">
                <input type="hidden" name="resident_id" id="delete-resident-id">
                <input type="hidden" name="resident_id" id="resident_id">
                <button type="submit" class="btn">Confirm</button>
            </form>
            <button class="btn" id="cancel-del">Cancel</button>
        </div>
    </div>


    <!-- Search Modal -->
    <div id="residentModal" class="modal-search hidden">
        <div class="modal-search-content">
            <span id="closeModal" class="modal-close">&times;</span>
            <h3>Select a Resident</h3>
            <input type="text" id="residentSearch" placeholder="Search by name..." />
            <div id="residentSearchResults"></div>
            <button id="confirmResidentBtn" disabled>Confirm</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.2/dist/sweetalert2.min.js"></script>
    <script src="js/admin-dashboard.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Elements
            const scheduleModal = document.getElementById('schedule-modal');
            const editModal = document.getElementById('edit-modal');
            const deleteModal = document.getElementById('delete-modal');
            const residentModal = document.getElementById('residentModal');
            const pageContent = document.getElementById('pageContent');
            const addResidentBtn = document.getElementById('addResidentBtn');
            const closeScheduleModal = document.getElementById('close-schedule-modal');
            const cancelSchedModal = document.getElementById('cancel-schedule');
            const closeEditModal = document.getElementById('close-edit-modal');
            const closeDeleteModal = document.getElementById('close-delete-modal');
            const closeResidentModal = document.getElementById('closeModal');
            const residentSearch = document.getElementById('residentSearch');
            const residentSearchResults = document.getElementById('residentSearchResults');
            const confirmResidentBtn = document.getElementById('confirmResidentBtn');
            const residentTableBody = document.getElementById('residentTableBody');
            let selectedResidentId = null;


            // Helpers
            const openModal = (modal) => {
                modal.style.display = 'block';
                pageContent.classList.add('blur');
            };

            const closeModal = (modal) => {
                modal.style.display = 'none';
                pageContent.classList.remove('blur');
            };

            // Add Event Listeners
            addResidentBtn?.addEventListener('click', () => openModal(residentModal));
            closeResidentModal?.addEventListener('click', () => closeModal(residentModal));
            closeScheduleModal?.addEventListener('click', () => closeModal(scheduleModal));
            closeEditModal?.addEventListener('click', () => closeModal(editModal));
            cancelSchedModal?.addEventListener('click', () => closeModal(scheduleModal));
            closeDeleteModal?.addEventListener('click', () => closeModal(deleteModal));

            window.addEventListener('click', (event) => {
                if (event.target === scheduleModal) closeModal(scheduleModal);
                if (event.target === editModal) closeModal(editModal);
                if (event.target === deleteModal) closeModal(deleteModal);
                if (event.target === residentModal) closeModal(residentModal);
            });

            // Search Residents
            residentSearch?.addEventListener('input', () => {
                const query = residentSearch.value.trim();
                if (query.length > 2) {
                    fetch(`/Brgy Zone 1/search-residents.php?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            residentSearchResults.innerHTML = '';

                            // Check if the server returned an error
                            if (data.error) {
                                const errorDiv = document.createElement('div');
                                errorDiv.textContent = data.error;
                                errorDiv.classList.add('error-message'); // Add a class for styling
                                residentSearchResults.appendChild(errorDiv);
                                confirmResidentBtn.disabled = true;
                                return;
                            }

                            // Populate search results with residents
                            data.forEach(resident => {
                                const div = document.createElement('div');
                                div.textContent = resident.fullname;
                                div.dataset.residentId = resident.id;
                                div.addEventListener('click', () => {
                                    selectedResidentId = resident.id;
                                    residentSearchResults.querySelectorAll('div').forEach(node => node.classList.remove('selected'));
                                    div.classList.add('selected');
                                    confirmResidentBtn.disabled = false;
                                });
                                residentSearchResults.appendChild(div);
                            });
                        })
                        .catch(err => console.error("Error fetching residents:", err));
                } else {
                    residentSearchResults.innerHTML = '';
                }
            });


            // Confirm Resident Selection
            confirmResidentBtn?.addEventListener('click', () => {
                if (selectedResidentId) {
                    fetch('/Brgy Zone 1/add-resident.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                residentId: selectedResidentId
                            })
                        })
                        .then(response => response.json())
                        .then(resident => {
                            const newRow = `
                        <tr>
                            <td>${resident.fullname}</td>
                            <td>${resident.email}</td>
                            <td>Eligible</td>
                            <td>
                                <button class='btn-sched' data-resident-id='${resident.id}'>Schedule</button>
                            </td>
                        </tr>
                    `;
                            residentTableBody.insertAdjacentHTML('beforeend', newRow);
                            closeModal(residentModal);
                            bindActionButtons();
                        })
                        .catch(err => console.error("Error adding resident:", err));
                }
            });

            // Bind Buttons for Actions
            const bindActionButtons = () => {
                document.querySelectorAll('.btn-sched').forEach(button => {
                    button.addEventListener('click', (e) => {
                        const residentId = e.target.getAttribute('data-resident-id');
                        document.getElementById('resident_id').value = residentId; // Assuming an input with this ID exists
                        openModal(scheduleModal);
                    });
                });

                document.querySelectorAll('.btn-edit').forEach(button => {
                    button.addEventListener('click', (e) => {
                        const residentId = e.target.getAttribute('data-resident-id');
                        fetch(`get_resident_assistance.php?resident_id=${residentId}`)
                            .then(response => response.json())
                            .then(data => {
                                document.getElementById('edit-resident-id').value = residentId;
                                document.getElementById('edit-pickup-date').value = data.pickup_date;
                                Object.entries(data.items || {}).forEach(([itemId, quantity]) => {
                                    const itemInput = document.getElementById(`edit-item_${itemId}`);
                                    if (itemInput) itemInput.value = quantity;
                                });
                                openModal(editModal);
                            })
                            .catch(err => console.error("Error fetching resident data:", err));
                    });
                });

                document.querySelectorAll('.btn-delete').forEach(button => {
                    button.addEventListener('click', (e) => {
                        const residentId = e.target.getAttribute('data-resident-id');
                        document.getElementById('delete-resident-id').value = residentId; // Assuming input with this ID exists
                        openModal(deleteModal);
                    });
                });
            };

            // Initialize
            bindActionButtons();
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