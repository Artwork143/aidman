<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIDMAN Login</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Add SweetAlert -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Font Awesome Icons -->
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-header">
            <h1 class="aidman-title">Welcome to AIDMAN</h1>
        </div>
        <form action="login_process.php" method="post">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="login-button">Login <i class="fas fa-sign-in-alt"></i></button>
            <p>Don't have an account? <a href="reg-forum.php" class="register-link">Register here</a></p>
        </form>
    </div>

    <!-- SweetAlert Logic -->
    <script>
        // Check if there's an error parameter in the URL
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');

        window.onload = function() {
            if (error === 'invalid_password') {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Password',
                    text: 'The password you entered is incorrect. Please try again.',
                    backdrop: false // Disable backdrop scrolling effect
                });
            } else if (error === 'no_user') {
                Swal.fire({
                    icon: 'error',
                    title: 'No User Found',
                    text: 'No account with that username was found. Please register or try again.',
                    backdrop: false // Disable backdrop scrolling effect
                });
            }
        };

        // Add transition effect when clicking register link
        document.querySelector('.register-link').addEventListener('click', function (e) {
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