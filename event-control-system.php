<?php include 'check_admin.php'; ?>

<?php
// Include database connection
include 'db_connect.php';

$alertScript = ''; // This variable will hold the SweetAlert JavaScript

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES["image"])) {
        $targetDir = "uploads/"; // Directory to save uploaded images
        $targetFile = $targetDir . basename($_FILES["image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if image file is an actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $alertScript = "Swal.fire({icon: 'error', title: 'Error', text: 'File is not an image.', customClass: 'event-control-swal'});";
            $uploadOk = 0;
        }

        // Generate a unique name for the file to avoid conflicts
        $targetFile = $targetDir . uniqid() . '.' . $imageFileType;

        // Check file size (20MB limit)
        if ($_FILES["image"]["size"] > 20000000) { // 20MB in bytes
            $alertScript = "Swal.fire({icon: 'error', title: 'Error', text: 'Sorry, your file is too large.', customClass: 'event-control-swal'});";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            $alertScript = "Swal.fire({icon: 'error', title: 'Error', text: 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.', customClass: 'event-control-swal'});";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $alertScript = "Swal.fire({icon: 'error', title: 'Error', text: 'Sorry, your file was not uploaded.', customClass: 'event-control-swal'});";
        } else {
            // If everything is ok, try to upload file
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                // Insert file path into database
                $sql = "INSERT INTO images (image_path) VALUES ('" . $conn->real_escape_string($targetFile) . "')";
                if ($conn->query($sql) === TRUE) {
                    $alertScript = "Swal.fire({icon: 'success', title: 'Success', text: 'The file has been uploaded.', customClass: 'event-control-swal'});";
                } else {
                    $alertScript = "Swal.fire({icon: 'error', title: 'Error', text: 'Database error: " . $conn->error . "', customClass: 'event-control-swal'});";
                }
            } else {
                $alertScript = "Swal.fire({icon: 'error', title: 'Error', text: 'Sorry, there was an error uploading your file. Please check the uploads directory permissions.', customClass: 'event-control-swal'});";
            }
        }
    } elseif (isset($_POST['delete'])) {
        // Handle image deletion
        $imagePath = $conn->real_escape_string($_POST['delete']);
        $sql = "DELETE FROM images WHERE image_path = '$imagePath'";
        if ($conn->query($sql) === TRUE) {
            if (file_exists($imagePath) && unlink($imagePath)) {
                $alertScript = "Swal.fire({icon: 'success', title: 'Deleted', text: 'Image deleted successfully.', customClass: 'event-control-swal'});";
            } else {
                $alertScript = "Swal.fire({icon: 'error', title: 'Error', text: 'Error deleting image file.', customClass: 'event-control-swal'});";
            }
        } else {
            $alertScript = "Swal.fire({icon: 'error', title: 'Error', text: 'Database error: " . $conn->error . "', customClass: 'event-control-swal'});";
        }
    }
}

// Fetch images from the database
$sql = "SELECT * FROM images"; // Assuming you have an 'images' table
$result = $conn->query($sql);
$images = [];

if ($result->num_rows > 0) {
    // Fetch all images
    while ($row = $result->fetch_assoc()) {
        $images[] = $row['image_path']; // Assuming image_path is a column in your images table
    }
}

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <li><a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="aid-dashboard.php"><i class="fas fa-chart-line"></i> Aid Priority Ranking</a></li>
                    <li><a href="inventory-dashboard.php"><i class="fas fa-warehouse"></i> Inventory System</a></li>
                    <li class="arrow-dropdown">
                        <div class="arrow-dropdown-toggle" id="account-control-link">
                            <a href="account_control.php" style="flex-grow: 1;">
                                <i class="fas fa-user-cog mr-2"></i> Account Control Panel Register
                            </a>
                            <i class="fas fa-chevron-down arrow-toggle"></i>
                        </div>
                        <div class="arrow-dropdown-content" id="dropdown-content">
                            <a href="account-management.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'account-management.php' ? 'active' : ''; ?>" id="account-management-link">
                                <i class="fa-solid fa-file-invoice"></i> Account Management
                            </a>
                        </div>
                    </li>
                    <li class="nav-item active"><a href="event-control-system.php"><i class="fas fa-calendar-alt fa-lg mr-2"></i> Event Control System</a></li>
                    <li><a href="assistance-scheduling.php"><i class="fas fa-calendar-check fa-lg mr-2"></i> Assistance Scheduling</a></li>
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

            <section class="event-control-cards-section">
                <div class="event-control-card-container">
                    <div class="event-control-card-header">
                        <h3>Event Control</h3>
                    </div>
                    <div class="event-control-card-body">
                        <div class="event-control-carousel-container">
                            <button class="event-control-arrow-btn left" onclick="previousSlide()">&#10094;</button>
                            <div class="event-control-carousel">
                                <?php foreach ($images as $index => $image): ?>
                                    <img src="<?php echo $image; ?>" alt="Event <?php echo $index + 1; ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>">
                                <?php endforeach; ?>
                            </div>
                            <button class="event-control-arrow-btn right" onclick="nextSlide()">&#10095;</button>
                        </div>
                        <form action="" method="post" enctype="multipart/form-data">
                            <input type="file" name="image" required>
                            <button type="submit" class="event-control-btn upload-btn">Upload Image</button>
                        </form>

                        <h4>Uploaded Images</h4>
                        <ul>
                            <?php foreach ($images as $image): ?>
                                <li>
                                    <img src="<?php echo $image; ?>" alt="Uploaded Image" style="width: 100px; height: auto;">
                                    <button type="button" class="event-control-btn delete-event-btn aligned-delete-btn" onclick="confirmDelete('<?php echo htmlspecialchars($image, ENT_QUOTES); ?>')">Delete</button>
                                    <form action="" method="post" id="deleteForm-<?php echo urlencode($image); ?>" style="display:none;">
                                        <input type="hidden" name="delete" value="<?php echo htmlspecialchars($image, ENT_QUOTES); ?>">
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Injecting the SweetAlert Script -->
    <script>
        <?php echo $alertScript; ?>
    </script>

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


        
        let currentSlide = 0;

        function showSlide(index) {
            const slides = document.querySelectorAll('.event-control-carousel img');
            if (index >= slides.length) currentSlide = 0;
            if (index < 0) currentSlide = slides.length - 1;

            slides.forEach((slide, i) => {
                slide.classList.remove('active');
                if (i === currentSlide) {
                    slide.classList.add('active');
                }
            });
        }

        function nextSlide() {
            currentSlide++;
            showSlide(currentSlide);
        }

        function previousSlide() {
            currentSlide--;
            showSlide(currentSlide);
        }

        // Show the first slide initially
        showSlide(currentSlide);

        // Confirm delete using SweetAlert
        function confirmDelete(imagePath) {
            Swal.fire({
                title: 'Are you sure you want to delete this?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                customClass: 'event-control-swal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const encodedPath = encodeURIComponent(imagePath);
                    document.getElementById('deleteForm-' + encodedPath).submit();
                }
            });
        }
    </script>
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