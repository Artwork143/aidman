
<?php
// Include database connection
include 'db_connect.php';

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
    <title>Resident Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Link to Font Awesome for icons -->
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
                    <li><a href="admin-dashboard.html"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                </ul>
            </nav>
        </aside>
        <main>
            <header>
                <h2></h2>
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
    </div>
    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('#carousel img');

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.remove('active');
                if (i === index) {
                    slide.classList.add('active');
                }
            });
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }

        function previousSlide() {
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(currentSlide);
        }

        // Automatically move to the next slide every 5 seconds
        setInterval(nextSlide, 5000);

        // Show the first slide initially
        showSlide(currentSlide);
    </script>
    <script src="js/resident-dashboard.js"></script>
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