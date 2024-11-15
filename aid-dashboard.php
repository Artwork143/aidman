<?php include 'check_admin.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Link to Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Link to SweetAlert -->
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
                    <li><a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li class="nav-item active"><a href="aid-dashboard.php"><i class="fas fa-chart-line"></i> Aid Priority Ranking</a></li>
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
            <!-- Container for card headers -->
            <div id="unique-card-container" class="card-container">
                <!-- Left card header -->
                <div class="card-header card-header-left">
                    <h3>Resident Registration Form</h3>
                    <form action="submit_aid.php" method="post">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required><br>
                        
                        <label for="damage_severity">Damage Severity (1-10):</label>
                        <input type="number" id="damage_severity" name="damage_severity" min="1" max="10" required><br>
                        
                        <label for="number_of_occupants">Number of Occupants:</label>
                        <input type="number" id="number_of_occupants" name="number_of_occupants" required><br>
                        
                        <label for="vulnerability">Vulnerability (1-10):</label>
                        <input type="number" id="vulnerability" name="vulnerability" min="1" max="10" required><br>
                        
                        <label for="income_level">Income Level (1-10):</label>
                        <input type="number" id="income_level" name="income_level" min="1" max="10" required><br>
                        
                        <label for="special_needs">Special Needs (1-10):</label>
                        <input type="number" id="special_needs" name="special_needs" min="1" max="10" required><br>
                        
                        <input type="submit" value="Submit">
                    </form>
                </div>

                <!-- Right card header -->
                <div class="card-header card-header-right">
                    <h3>Leaderboard</h3>
                    <button onclick="printLeaderboard()" class="btn-print-leaderboard" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; transition: background-color 0.3s, box-shadow 0.3s, filter 0.3s;">Print Leaderboard</button>
                    <style>
                        .btn-print-leaderboard:hover {
                            background-color: #45a049;
                            box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
                            filter: brightness(1.2);
                        }
                    </style>
                    <style>
                        .btn-print-leaderboard:hover {
                            background-color: #45a049;
                            box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
                            animation: shine 1s ease-in-out;
                        }
                        @keyframes shine {
                            0% {
                                box-shadow: 0 0 5px rgba(255, 255, 255, 0.2);
                            }
                            50% {
                                box-shadow: 0 0 20px rgba(255, 255, 255, 0.6);
                            }
                            100% {
                                box-shadow: 0 0 5px rgba(255, 255, 255, 0.2);
                            }
                        }
                    </style>
                    <style>
                        .btn-print-leaderboard:hover {
                            background-color: #45a049;
                            box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
                            animation: shine 1s ease-in-out;
                        }
                        @keyframes shine {
                            0% {
                                box-shadow: 0 0 5px rgba(255, 255, 255, 0.2);
                            }
                            50% {
                                box-shadow: 0 0 20px rgba(255, 255, 255, 0.6);
                            }
                            100% {
                                box-shadow: 0 0 5px rgba(255, 255, 255, 0.2);
                            }
                        }
                    </style>
                    <ul class="leaderboard" id="leaderboard">
                        <?php
                        // Example PHP to fetch data from the database
                        $pdo = new PDO('mysql:host=localhost;dbname=aidman-db', 'root', '');
                        $stmt = $pdo->query('SELECT id, name, total_score FROM residents ORDER BY total_score DESC');
                        $rankings = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <?php foreach ($rankings as $index => $resident): ?>
                        <li class="person">
                            <span class="icon"><?php echo $index + 1; ?></span>
                            <span class="nickname"><?php echo htmlspecialchars($resident['name']); ?></span>
                            <span class="score"><?php echo htmlspecialchars($resident['total_score']); ?></span>
                            <a href="#" class="btn btn-leaderboard-edit edit-btn" data-id="<?php echo urlencode($resident['id']); ?>">Edit</a>
                            <a href="#" class="btn btn-leaderboard-delete delete-btn" data-id="<?php echo urlencode($resident['id']); ?>">Delete</a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </main>
    </div>
    <script src="js/admin-dashboard.js"></script>
    <script>
        function printLeaderboard() {
            // Open a new window to print leaderboard
            const leaderboard = document.getElementById('leaderboard').cloneNode(true);
            const editButtons = leaderboard.querySelectorAll('.edit-btn, .delete-btn');
            editButtons.forEach(button => button.remove());
            leaderboard.style.fontFamily = 'Arial, sans-serif';
            leaderboard.style.width = '100%';
            leaderboard.style.margin = '0 auto';
            leaderboard.style.borderCollapse = 'collapse';
            const rows = leaderboard.querySelectorAll('li');
            rows.forEach((row, index) => {
                row.style.display = 'flex';
                row.style.justifyContent = 'space-between';
                row.style.padding = '10px';
                row.style.borderBottom = '1px solid #ccc';
                if (index % 2 === 0) {
                    row.style.backgroundColor = '#f9f9f9';
                }
                row.style.fontSize = '16px';
            });
            const printContents = leaderboard.outerHTML;
            const originalContents = document.body.innerHTML;
            const printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Print Leaderboard</title>');
            printWindow.document.write('<style>body { font-family: Arial, sans-serif; margin: 20px; } h2 { text-align: center; font-size: 26px; margin-bottom: 10px; } h3 { text-align: center; font-size: 24px; margin-bottom: 20px; } .leaderboard { list-style-type: none; padding: 0; margin: 0; width: 100%; } .leaderboard li { display: flex; justify-content: space-between; padding: 10px; border-bottom: 1px solid #ccc; } .leaderboard li:nth-child(even) { background-color: #f9f9f9; } .icon { font-weight: bold; } .nickname, .score { font-size: 16px; }</style>');
            printWindow.document.write('<style>body { font-family: Arial, sans-serif; margin: 20px; } h3 { text-align: center; font-size: 24px; margin-bottom: 20px; } .leaderboard { list-style-type: none; padding: 0; margin: 0; width: 100%; } .leaderboard li { display: flex; justify-content: space-between; padding: 10px; border-bottom: 1px solid #ccc; } .leaderboard li:nth-child(even) { background-color: #f9f9f9; } .icon { font-weight: bold; } .nickname, .score { font-size: 16px; }</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write('<h2>PRIORITY RANK LIST BASE ON CRITERIA</h2>');
            printWindow.document.write(printContents);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }
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