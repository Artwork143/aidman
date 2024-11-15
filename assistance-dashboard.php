<?php
// Database connection
require 'db_connect.php';
$pdo = new PDO('mysql:host=localhost;dbname=aidman-db', 'root', '');

// Fetch registered users
$sql = "SELECT * FROM users WHERE role = 'Resident'";
$usersResult = $conn->query($sql);
$allUsers = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

// Fetch inventory items
$inventorySql = "SELECT * FROM inventory";
$inventoryResult = $conn->query($inventorySql);
$inventoryItems = $inventoryResult->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule and Distribution System</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .schedule-container {
            width: 80%;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h3 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .form-group {
            margin-bottom: 10px;
        }
        input[type="datetime-local"], select {
            padding: 8px;
            width: 80%;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 8px 16px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
        button[type="button"] {
            background-color: #dc3545;
        }
        button[type="button"]:hover {
            background-color: #c82333;
        }
        .inventory-popup, .user-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            width: 50%;
        }
        .close-btn {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            float: right;
        }
    </style>
</head>
<body>
    <div class="schedule-container">
        <h3>Schedule and Aid Distribution</h3>
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Schedule Time</th>
                    <th>Supplies Given</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $usersResult->fetch_assoc()): ?>
                <tr id="user-row-<?php echo $row['id']; ?>">
                    <td>
                        <button type="button" onclick="showUserList(<?php echo $row['id']; ?>)"><?php echo htmlspecialchars($row['fullname']); ?></button>
                    </td>
                    <td id="email-cell-<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <form action="" method="POST">
                            <input type="datetime-local" name="schedule_time_<?php echo $row['id']; ?>" required>
                        </form>
                    </td>
                    <td id="supplies-cell-<?php echo $row['id']; ?>">
                        <button type="button" id="pick-supplies-btn-<?php echo $row['id']; ?>" onclick="showInventory(<?php echo $row['id']; ?>)">Pick the Supplies Here</button>
                    </td>
                    <td>
                        <button type="submit" name="save_schedule" value="<?php echo $row['id']; ?>">Save</button>
                        <button type="button" onclick="sendNotification('<?php echo $row['email']; ?>')">Notify</button>
                        <button type="button" onclick="editSchedule('<?php echo $row['id']; ?>')">Edit</button>
                        <button type="button" onclick="deleteSchedule('<?php echo $row['id']; ?>')">Delete</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div id="inventory-popup" class="inventory-popup">
        <button class="close-btn" onclick="closeInventory()">Close</button>
        <h3>Available Supplies</h3>
        <form id="inventory-form">
            <?php foreach ($inventoryItems as $item): ?>
                <div class="form-group">
                    <label for="<?php echo strtolower($item['name']); ?>"><?php echo htmlspecialchars($item['name']); ?> (<?php echo htmlspecialchars($item['unit']); ?>, Available: <?php echo htmlspecialchars($item['quantity']); ?>):</label>
                    <input type="number" name="<?php echo strtolower($item['name']); ?>" id="<?php echo strtolower($item['name']); ?>" min="0" max="<?php echo htmlspecialchars($item['quantity']); ?>">
                </div>
            <?php endforeach; ?>
            <button type="button" onclick="saveSupplies()">Save Supplies</button>
        </form>
    </div>

    <div id="user-popup" class="user-popup">
        <button class="close-btn" onclick="closeUserList()">Close</button>
        <h3>Select a Resident</h3>
        <ul id="user-list">
            <?php foreach ($allUsers as $user): ?>
                <li>
                    <button type="button" onclick="selectUser('<?php echo $user['id']; ?>', '<?php echo htmlspecialchars($user['fullname']); ?>', '<?php echo htmlspecialchars($user['email']); ?>')">
                        <?php echo htmlspecialchars($user['fullname']) . ' (' . htmlspecialchars($user['email']) . ')'; ?>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <?php
    // Handle saving schedule and distribution
    if (isset($_POST['save_schedule'])) {
        $userId = $_POST['save_schedule'];
        $scheduleTime = $_POST['schedule_time_' . $userId];
        $suppliesGiven = [];

        foreach ($inventoryItems as $item) {
            $itemName = strtolower($item['name']);
            $itemAmount = isset($_POST[$itemName]) ? intval($_POST[$itemName]) : 0;

            // Update inventory quantities
            if ($itemAmount > 0) {
                $updateStmt = $pdo->prepare('UPDATE inventory SET quantity = quantity - ? WHERE name = ?');
                $updateStmt->execute([$itemAmount, $item['name']]);

                // Record the distribution
                $insertDistributionStmt = $pdo->prepare('INSERT INTO distributions (resident_id, item, quantity, schedule_time) VALUES (?, ?, ?, ?)');
                $insertDistributionStmt->execute([$userId, $item['name'], $itemAmount, $scheduleTime]);

                // Add to supplies given
                $suppliesGiven[] = $item['name'] . ' (' . $itemAmount . ' ' . $item['unit'] . ')';
            }
        }

        // Update the supplies cell after saving
        if (!empty($suppliesGiven)) {
            echo "<script>
                document.getElementById('supplies-cell-$userId').innerHTML = '" . implode(', ', $suppliesGiven) . "';
                document.getElementById('pick-supplies-btn-$userId').style.display = 'none';
            </script>";
        }

        echo "<p>Schedule and distribution recorded successfully.</p>";
    }

    // Email notification function
    function sendNotification($email) {
        // Prepare email content
        $subject = "Aid Distribution Notification";
        $message = "You have been scheduled to receive aid. Please check your schedule.";
        $headers = "From: no-reply@aidman.com";

        // Send email
        mail($email, $subject, $message, $headers);
        echo "<p>Notification sent to $email</p>";
    }
    ?>

    <script>
        function showInventory(userId) {
            const popup = document.getElementById('inventory-popup');
            popup.setAttribute('data-user-id', userId);
            popup.style.display = 'block';
        }

        function closeInventory() {
            const popup = document.getElementById('inventory-popup');
            popup.style.display = 'none';
        }

        function saveSupplies() {
            const popup = document.getElementById('inventory-popup');
            const userId = popup.getAttribute('data-user-id');
            let supplies = [];
            const inputs = document.querySelectorAll('#inventory-form input[type="number"]');

            inputs.forEach(input => {
                if (input.value > 0) {
                    supplies.push({ name: input.name, amount: input.value });
                }
            });

            // Update supplies cell in the table
            const suppliesCell = document.getElementById(`supplies-cell-${userId}`);
            suppliesCell.innerHTML = supplies.map(s => s.name + ' (' + s.amount + ')').join(', ');
            
            // Hide "Pick Supplies Here" button
            document.getElementById(`pick-supplies-btn-${userId}`).style.display = 'none';

            closeInventory();
        }

        function showUserList(userId) {
            const popup = document.getElementById('user-popup');
            popup.setAttribute('data-user-id', userId);
            popup.style.display = 'block';
        }

        function closeUserList() {
            const popup = document.getElementById('user-popup');
            popup.style.display = 'none';
        }

        function selectUser(userId, fullname, email) {
            const targetUserId = document.getElementById('user-popup').getAttribute('data-user-id');
            document.querySelector(`#user-row-${targetUserId} td:nth-child(1) button`).innerHTML = fullname;
            document.getElementById(`email-cell-${targetUserId}`).innerHTML = email;
            closeUserList();
        }

        function sendNotification(email) {
            alert('Notification sent to ' + email);
        }

        function editSchedule(userId) {
            alert('Edit function for user ID ' + userId);
        }

        function deleteSchedule(userId) {
            alert('Delete function for user ID ' + userId);
        }
    </script>
</body>
</html>