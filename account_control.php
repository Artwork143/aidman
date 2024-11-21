<?php include 'check_admin.php'; ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                    <li><a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="aid-dashboard.php"><i class="fas fa-chart-line"></i> Aid Priority Ranking</a></li>
                    <li><a href="inventory-dashboard.php"><i class="fas fa-warehouse"></i> Inventory System</a></li>
                    <li class="arrow-dropdown">
                        <div class="arrow-dropdown-toggle" id="account-control-link">
                            <a style="flex-grow: 1;">
                                <i class="fas fa-user-cog mr-2"></i> Account Control Panel
                            </a>
                            <i class="fas fa-chevron-down arrow-toggle"></i>
                        </div>
                        <div class="arrow-dropdown-content">
                            <a href="account_control.php" class="nav-item active"><i class="fa-solid fa-user-plus"></i> Register Account</a>
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

            <!-- Left Card Header for Control -->
            <div class="control-header">
                <h3 class="control-title">Control Header</h3>
                <form action="control_registration.php" method="post" class="control-form">
                    <input type="text" name="fullname" placeholder="Full Name" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <!-- Select Role -->
                    <label for="role">Select Role:</label>
                    <select name="role" id="role" required>
                        <option value="Resident">Resident</option>
                        <option value="Official">Official</option>
                    </select>
                    <button type="submit" class="control-btn">Register</button>
                </form>
            </div>

        </main>
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        // Dropdown behavior for Account Control Panel
        const accountDropdownToggle = document.querySelector(".arrow-dropdown-toggle");
        const accountDropdownParent = document.querySelector(".arrow-dropdown");
        
        if (accountDropdownParent && window.location.pathname.includes("/account_control")) {
            accountDropdownParent.classList.add("active");
        }

        if (accountDropdownToggle) {
            accountDropdownToggle.addEventListener("click", function () {
                const parent = this.closest(".arrow-dropdown-account");
                parent.classList.toggle("active");
            });
        }

        // Dropdown behavior for Assistance Scheduling
        const assistanceDropdownToggle = document.querySelector(".arrow-dropdown-toggle-assistance");
        if (assistanceDropdownToggle) {
            assistanceDropdownToggle.addEventListener("click", function () {
                const parent = this.closest(".arrow-dropdown-assistance");
                parent.classList.toggle("active");
            });
        }
    });
</script>

    <script src="js/admin-dashboard.js"></script>

    <!-- Logout Confirmation Modal -->
    <div id="logout-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Are you sure you want to logout?</h2>
            <button id="confirm-logout" class="btn">Yes</button>
            <button id="cancel-logout" class="btn">No</button>
        </div>
    </div>
</body>

</html>