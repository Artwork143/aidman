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
                    <li class="nav-item active"><a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
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
            <section class="main-content">
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