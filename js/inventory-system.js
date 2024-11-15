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
