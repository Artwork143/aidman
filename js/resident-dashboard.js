document.addEventListener('DOMContentLoaded', function () {
    // Handle dropdown menu for user profile
    const userIcon = document.querySelector('.dropdown i');
    const dropdownMenu = document.querySelector('.dropdown-menu');

    if (userIcon && dropdownMenu) {
        userIcon.addEventListener('click', function (event) {
            event.stopPropagation(); // Prevent event from bubbling up
            dropdownMenu.classList.toggle('show');
        });

        document.addEventListener('click', function (event) {
            if (!userIcon.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
    }

    // JavaScript for logout confirmation modal
    const logoutLink = document.getElementById('logout-link');
    const modal = document.getElementById('logout-modal');
    const closeModal = document.querySelector('#logout-modal .close');
    const confirmLogout = document.getElementById('confirm-logout');
    const cancelLogout = document.getElementById('cancel-logout');

    if (logoutLink && modal) {
        logoutLink.addEventListener('click', (event) => {
            event.preventDefault(); // Prevent immediate navigation
            modal.style.display = 'block'; // Show the modal
        });

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
                window.location.href = logoutLink.href; // Proceed with logout
            });
        }

        // Close the modal if the user clicks outside of it
        window.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.style.display = 'none'; // Hide the modal
            }
        });
    }

    // Function to load account info data when clicking "Account Information"
    const viewProfile = document.getElementById('view-profile');
    if (viewProfile) {
        viewProfile.addEventListener('click', function (e) {
            e.preventDefault();
            fetch('account-information.php')
                .then(response => response.text())
                .then(data => {
                    // Redirect to the account information page
                    window.location.href = 'account-information.php';
                })
                .catch(error => {
                    console.error('Error loading profile:', error);
                });
        });
    }
});
