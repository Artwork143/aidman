<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Complete</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <div class="message-box">
        <div class="confetti">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
        <?php
        if (isset($_GET['status']) && $_GET['status'] == 'success') {
            echo '<div class="icon">🎉</div>';
            echo '<h1 class="success">User Registered Successfully</h1>';
            echo '<p>You have successfully registered the user! You will be redirected to the account control page shortly.</p>';
        } else {
            echo '<div class="icon">❌</div>';
            echo '<h1 class="error">Registration Failed</h1>';
            $reason = isset($_GET['reason']) ? htmlspecialchars($_GET['reason']) : '';
            $name = isset($_GET['fullname']) ? htmlspecialchars($_GET['fullname']) : '';
            $email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
            $username = isset($_GET['username']) ? htmlspecialchars($_GET['username']) : '';

            // Determine the duplicate field
            if (strpos($reason, 'duplicate_') === 0) {
                $duplicate = str_replace('duplicate_', '', $reason);
                echo '<p>Registration failed: Duplicate ' . $duplicate . '.</p>';
            } else {
                echo '<p>Registration failed due to an unexpected error. Please try again later.</p>';
                $duplicate = '';
            }

            // Redirect URL for the back button
            $redirect_url = "account_control.php?duplicate=$duplicate&fullname=" . urlencode($name) . "&email=" . urlencode($email) . "&username=" . urlencode($username);
            echo '<p>If you are not redirected automatically, <a href="' . $redirect_url . '">click here</a>.</p>';
        }
        ?>
    </div>

    <script>
        // Redirect after 5 seconds for successful registration
        <?php if (isset($_GET['status']) && $_GET['status'] == 'success') { ?>
        setTimeout(function() {
            window.location.href = 'account_control.php';
        }, 5000);
        <?php } ?>
    </script>
</body>
</html>
