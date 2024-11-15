document.addEventListener('DOMContentLoaded', function() {
    const userIcon = document.querySelector('.dropdown i');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    // Handle dropdown menu
    userIcon.addEventListener('click', function() {
        dropdownMenu.classList.toggle('show');
    });

    document.addEventListener('click', function(event) {
        if (!userIcon.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.remove('show');
        }
    });


    // JavaScript for logout confirmation
    const logoutLink = document.getElementById('logout-link');
    const modal = document.getElementById('logout-modal');
    const closeModal = document.querySelector('#logout-modal .close');
    const confirmLogout = document.getElementById('confirm-logout');
    const cancelLogout = document.getElementById('cancel-logout');

    if (logoutLink) {
        logoutLink.addEventListener('click', (event) => {
            event.preventDefault(); // Prevent immediate navigation
            modal.style.display = 'block'; // Show modal
        });
    }

    closeModal.addEventListener('click', () => {
        modal.style.display = 'none'; // Hide modal
    });

    cancelLogout.addEventListener('click', () => {
        modal.style.display = 'none'; // Hide modal
    });

    confirmLogout.addEventListener('click', () => {
        window.location.href = logoutLink.href; // Proceed with logout
    });

    // Close the modal if clicking outside of it
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none'; // Hide modal
        }
    });
});

// Function to load account info data when clicking "Account Information"
document.getElementById('view-profile').addEventListener('click', function (e) {
    e.preventDefault();
    fetch('account-information.php')
        .then(response => response.text())
        .then(data => {
            // Here you would typically populate the account information page with the user data
            window.location.href = 'account-information.php';
        })
        .catch(error => {
            console.error('Error loading profile:', error);
        });
});