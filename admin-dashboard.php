<?php include 'check_admin.php'; ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="css/admin-dashboard.css"> <!-- Updated link -->
    <!-- Link to Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <img src="logo.jpg" alt="Barangay Logo">
                <h1>AIDMAN</h1>
            </div>
            <nav>
                <ul>
                    <li class="nav-item active">
                        <a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    </li>
                    <li>
                        <a href="aid-dashboard.php"><i class="fas fa-chart-line"></i> Aid Priority Ranking</a>
                    </li>
                    <li>
                        <a href="inventory-dashboard.php"><i class="fas fa-warehouse"></i> Inventory System</a>
                    </li>
                    <li class="arrow-dropdown">
                        <div class="arrow-dropdown-toggle" id="account-control-link">
                            <a style="flex-grow: 1;">
                                <i class="fas fa-user-cog mr-2"></i> Account Control Panel
                            </a>
                            <i class="fas fa-chevron-down arrow-toggle"></i>
                        </div>
                        <div class="arrow-dropdown-content">
                            <a href="account_control.php"><i class="fa-solid fa-user-plus"></i> Register Account</a>
                            <a href="account-management.php"><i class="fa-solid fa-file-invoice"></i> Account Management</a>
                        </div>
                    </li>
                    <li>
                        <a href="event-control-system.php"><i class="fas fa-calendar-alt fa-lg mr-2"></i> Event Control System</a>
                    </li>
                    <li class="arrow-dropdown-assistance">
                        <div class="arrow-dropdown-toggle-assistance" id="assistance-scheduling-link">
                            <a style="flex-grow: 1;">
                                <i class="fas fa-calendar-check fa-lg mr-2"></i> Assistance Scheduling
                            </a>
                            <i class="fas fa-chevron-down arrow-toggle-assistance"></i>
                        </div>
                        <div class="arrow-dropdown-content-assistance">
                            <a href="assistance-scheduling.php"><i class="fa-solid fa-calendar-plus"></i> Schedule Assistance</a>
                            <a href="assistance-history.php"><i class="fa-solid fa-history"></i> Assistance History</a>
                        </div>
                    </li>
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
            <section class="main-content dropdown">
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>All Total Registered</h3>
                    <p id="total-all">Loading...</p>
                </div>
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3>Total Official Account</h3>
                    <p id="total-officials">Loading...</p>
                </div>
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3>Total Resident Account</h3>
                    <p id="total-residents">Loading...</p>
                </div>
            </section>
        </main>
    </div>
    <script src="js/admin-dashboard.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Notification Dropdown
            const notificationBell = document.getElementById('notification-bell');
            const notificationDropdown = document.getElementById('notification-dropdown');
            if (notificationBell) {
                notificationBell.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notificationDropdown.classList.toggle('show');

                    // Close profile menu if open
                    const profileMenu = document.getElementById('profile-menu');
                    if (profileMenu && profileMenu.classList.contains('show')) {
                        profileMenu.classList.remove('show');
                    }
                });
            }

            // Profile Dropdown
            const profileDropdown = document.getElementById('profile-dropdown');
            const profileMenu = document.getElementById('profile-menu');
            if (profileDropdown) {
                profileDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileMenu.classList.toggle('show');

                    // Close notification dropdown if open
                    if (notificationDropdown && notificationDropdown.classList.contains('show')) {
                        notificationDropdown.classList.remove('show');
                    }
                });
            }

            // Close dropdowns when clicking outside
            document.addEventListener('click', function() {
                if (notificationDropdown) notificationDropdown.classList.remove('show');
                if (profileMenu) profileMenu.classList.remove('show');
            });

            // Logout Modal
            const logoutLink = document.getElementById('logout-link');
            const modal = document.getElementById('logout-modal');
            const closeModal = document.querySelector('#logout-modal .close');
            const confirmLogout = document.getElementById('confirm-logout');
            const cancelLogout = document.getElementById('cancel-logout');

            if (logoutLink && modal) {
                logoutLink.addEventListener('click', (e) => {
                    e.preventDefault(); // Prevent immediate navigation
                    modal.style.display = 'block'; // Show the modal
                });
            }

            if (closeModal) {
                closeModal.addEventListener('click', () => {
                    modal.style.display = 'none'; // Hide the modal
                });
            }

            if (cancelLogout) {
                cancelLogout.addEventListener('click', () => {
                    modal.style.display = 'none'; // Hide the modal
                });
            }

            if (confirmLogout) {
                confirmLogout.addEventListener('click', () => {
                    window.location.href = logoutLink.href; // Redirect to logout URL
                });
            }

            // Close the modal if clicking outside of it
            window.addEventListener('click', (event) => {
                if (event.target === modal) {
                    modal.style.display = 'none'; // Hide the modal
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            // Dropdown behavior for Account Control Panel
            const accountDropdownToggle = document.querySelector(".arrow-dropdown-toggle-account");
            if (accountDropdownToggle) {
                accountDropdownToggle.addEventListener("click", function() {
                    const parent = this.closest(".arrow-dropdown-account");
                    parent.classList.toggle("active");
                });
            }

            // Dropdown behavior for Assistance Scheduling
            const assistanceDropdownToggle = document.querySelector(".arrow-dropdown-toggle-assistance");
            if (assistanceDropdownToggle) {
                assistanceDropdownToggle.addEventListener("click", function() {
                    const parent = this.closest(".arrow-dropdown-assistance");
                    parent.classList.toggle("active");
                });
            }
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
</div>

</html>