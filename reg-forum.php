<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Add SweetAlert -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Font Awesome Icons -->
</head>
<body class="registration-page">
<a href="home-page.html" class="back-button"><i class="fas fa-arrow-left"></i> Back</a>
    <div class="register-container">
        <div class="register-header">
            <h1 class="aidman-title">Register to AIDMAN</h1>
        </div>
        <?php
        // Retrieve values from query parameters and handle duplicates
        $fullname = isset($_GET['fullname']) ? htmlspecialchars($_GET['fullname']) : '';
        $email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
        $username = isset($_GET['username']) ? htmlspecialchars($_GET['username']) : '';
        $duplicate = isset($_GET['duplicate']) ? $_GET['duplicate'] : '';
        ?>
        <form action="submit_registration_control.php" method="post">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="fullname" placeholder="Full Name" value="<?= $fullname ?>" class="<?= $duplicate === 'fullname' ? 'input-error' : '' ?>" required>
            </div>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" value="<?= $email ?>" class="<?= $duplicate === 'email' ? 'input-error' : '' ?>" required>
            </div>
            <div class="input-group">
                <i class="fas fa-user-circle"></i>
                <input type="text" name="username" placeholder="Username" value="<?= $username ?>" class="<?= $duplicate === 'username' ? 'input-error' : '' ?>" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <!-- Hidden role field set to 'Resident' -->
            <input type="hidden" name="role" value="Resident">
            <button type="submit" class="register-button">Register <i class="fas fa-user-plus"></i></button>
        </form>
        <a href="login.php" class="login-link">Already have an account? Log in here</a>
    </div>

    <!-- SweetAlert Logic -->
    <script>
        // Add transition effect when clicking login link
        document.querySelector('.login-link').addEventListener('click', function (e) {
            e.preventDefault();
            const targetUrl = this.getAttribute('href');

            // Create the expanding circle animation
            const circle = document.createElement('div');
            circle.classList.add('transition-circle');
            document.body.appendChild(circle);

            // Set the position of the circle to the click position
            const rect = this.getBoundingClientRect();
            circle.style.left = `${rect.left + rect.width / 2}px`;
            circle.style.top = `${rect.top + rect.height / 2}px`;

            // Start the animation
            circle.addEventListener('animationend', () => {
                window.location.href = targetUrl;
            });
        });
    </script>
</body>
</html>