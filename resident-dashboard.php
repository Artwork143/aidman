<?php
session_start(); // Start session at the beginning

// Include database connection
include 'db_connect.php';

// Check if the connection is successful
if (!$conn) {
    die("Database connection failed: " . $conn->connect_error);
}

// Verify if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: User is not logged in. Please log in first.");
}

// Fetch the logged-in user's ID
$resident_id = $_SESSION['user_id'];

// Fetch images from the database
$sql = "SELECT * FROM images"; // Assuming you have an 'images' table
$result = $conn->query($sql);
$images = [];

if ($result && $result->num_rows > 0) {
    // Fetch all images
    while ($row = $result->fetch_assoc()) {
        $images[] = $row['image_path']; // Assuming image_path is a column in your images table
    }
}

// Fetch notifications for the logged-in resident from the `scheduled_assistance` table
// Fetch notifications for the logged-in resident
$notifications_sql = "
    SELECT 
        sr.id AS resident_id, 
        sr.fullname, 
        sr.assistance_status, 
        sa.notification_message 
    FROM 
        schedule_residents sr
    JOIN 
        scheduled_assistance sa 
    ON 
        sr.id = sa.resident_id
    WHERE 
        sr.id = ? 
        AND sr.assistance_status NOT IN ('pickup', 'received')
";

// Prepare the statement and check for errors
if (!$stmt = $conn->prepare($notifications_sql)) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $resident_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row; // Collect notifications
    }
}

// Check if there are pending notifications
$has_new_notifications = count($notifications) > 0;

// Close the statement
$stmt->close();


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Dashboard</title>
    <link rel="stylesheet" href="assistance.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.2/dist/sweetalert2.min.css">
    <!-- Link to Font Awesome for icons -->
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
    </style>
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
                    <li><a href="admin-dashboard.html"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                </ul>
            </nav>
        </aside>
        <main>
            <header>
                <h2>Resident</h2>
                <div class="header-right">
                    <!-- Notification Bell -->
                    <div class="notification-bell" id="notification-bell">
                        <i class="fas fa-bell" style="color: <?php echo $has_new_notifications ? 'red' : '#555'; ?>;"></i>
                        <?php if ($has_new_notifications): ?>
                            <span class="badge"><?php echo count($notifications); ?></span>
                        <?php endif; ?>
                        <div class="dropdown-menu notifications" id="notification-dropdown">
                            <?php if ($has_new_notifications): ?>
                                <?php foreach ($notifications as $notification): ?>
                                    <div class="dropdown-item" data-id="<?php echo $notification['resident_id']; ?>">
                                        <p>
                                            <strong>Status:</strong> <?php echo htmlspecialchars($notification['assistance_status']); ?><br>
                                            <?php echo htmlspecialchars($notification['notification_message']); ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>

                            <?php else: ?>
                                <div class="dropdown-item">
                                    <p>No new notifications</p>
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
            <section class="event-control-carousel-container">
                <button class="event-control-arrow-btn left" onclick="previousSlide()">&#10094;</button>
                <div class="event-control-carousel" id="carousel">
                    <?php foreach ($images as $index => $image): ?>
                        <img src="<?php echo $image; ?>" alt="Event <?php echo $index + 1; ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>">
                    <?php endforeach; ?>
                </div>
                <button class="event-control-arrow-btn right" onclick="nextSlide()">&#10095;</button>
            </section>
        </main>

        <!-- Logout Confirmation Modal -->
        <div id="logout-modal" class="logout-modal">
            <div class="logout-content">
                <span class="close">&times;</span>
                <h2>Are you sure you want to logout?</h2>
                <button id="confirm-logout" class="btn">Yes</button>
                <button id="cancel-logout" class="btn">No</button>
            </div>
        </div>

        <!-- Notification Details Modal -->
        <div id="notification-modal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close" id="close-assistance">&times;</span>
                <h2>Assistance Details</h2>
                <p><strong>Scheduled Pickup Date:</strong> <span id="pickup-date"></span></p>
                <p><strong>Supplies:</strong></p>
                <ul id="supply-list"></ul>
                <button id="confirm-assistance" class="btn">Confirm Assistance</button>
                <button id="cancel-assistance" class="btn">Cancel</button>
            </div>
        </div>


    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.2/dist/sweetalert2.min.js"></script>
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

        document.addEventListener('DOMContentLoaded', function() {
            const notificationItems = document.querySelectorAll('.dropdown-item[data-id]');
            const confirmAssistance = document.getElementById('confirm-assistance');
            const notificationModal = document.getElementById('notification-modal');
            const pickupDate = document.getElementById('pickup-date');
            const supplyList = document.getElementById('supply-list');
            const closeAssistance = document.getElementById('close-assistance');
            const cancelAssistance = document.getElementById('cancel-assistance');

            let assistanceDetails = null; // To store the fetched assistance details
            let notificationId = null; // To store the current notification_id

            notificationItems.forEach(item => {
                item.addEventListener('click', function() {
                    notificationId = this.getAttribute('data-id'); // Store the notification_id when clicked

                    if (!notificationId) {
                        alert('Notification ID is missing.');
                        return;
                    }

                    console.log(`Fetching details for notification_id: ${notificationId}`);

                    fetch('fetch-assistance-details.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `notification_id=${encodeURIComponent(notificationId)}`,
                        })
                        .then(response => response.text())
                        .then(rawData => {
                            console.log('Raw Data:', rawData);

                            const data = JSON.parse(rawData);

                            if (data.status === 'success') {
                                assistanceDetails = data; // Store details for use in confirmation
                                pickupDate.textContent = data.pickup_date;

                                supplyList.innerHTML = ''; // Clear existing list
                                data.supplies.forEach(supply => {
                                    const listItem = document.createElement('li');
                                    listItem.textContent = `${supply.quantity} ${supply.unit} of ${supply.item_name}`;
                                    supplyList.appendChild(listItem);
                                });

                                notificationModal.style.display = 'block';
                            } else {
                                alert(data.message || 'Failed to fetch assistance details.');
                            }
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            alert('An error occurred while fetching assistance details.');
                        });
                });
            });

            // Confirm Assistance - Deduct quantities
            confirmAssistance.addEventListener('click', function() {
                if (!assistanceDetails || !assistanceDetails.supplies) {
                    alert('No assistance details available to confirm.');
                    return;
                }

                const deductionData = {
                    notification_id: notificationId, // Include the notification_id in the request
                    supplies: assistanceDetails.supplies.map(supply => ({
                        item_id: supply.item_id, // Assuming `item_id` is available in the response
                        quantity: supply.quantity
                    }))
                };

                fetch('deduct-inventory.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(deductionData),
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.status === 'success') {
                            alert('Assistance confirmed and inventory updated.');
                            notificationModal.style.display = 'none';
                            location.reload();
                        } else {
                            alert(result.message || 'Failed to confirm assistance.');
                        }
                    })
                    .catch(error => {
                        console.error('Error confirming assistance:', error);
                        alert('An error occurred while confirming assistance.');
                    });
            });

            // Close modal on cancel or close
            cancelAssistance.onclick = closeAssistance.onclick = function() {
                notificationModal.style.display = 'none';
            };

            // Close the modal if clicking outside of it
            window.addEventListener('click', (event) => {
                if (event.target === notificationModal) {
                    notificationModal.style.display = 'none';
                }
            });
        });

        // Submit the edit form and show a Swal.fire success message upon successful update
        document.getElementById('confirm-assistance').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission

            // Create a FormData object to send the form data
            let formData = new FormData(this);

            // Send the form data via AJAX (fetch)
            fetch('deduct-inventory.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message with Swal.fire
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.success,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Optionally, close the modal or reload the page
                                document.getElementById('notification-modal').style.display = 'none';
                                document.getElementById('pageContent').classList.remove('blur');
                                // Optionally, reload the page to reflect the updates
                                // location.reload();
                            }
                        });
                    } else if (data.error) {
                        // Handle error response from PHP
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.error,
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong. Please try again.',
                        confirmButtonText: 'OK'
                    });
                });
        });
    </script>

    <script src="js/resident-dashboard.js"></script>
</body>

</html>