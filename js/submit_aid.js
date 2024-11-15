document.addEventListener('DOMContentLoaded', function() {
    const redirectBtn = document.getElementById('redirect-btn');
    if (redirectBtn) {
        redirectBtn.addEventListener('click', function() {
            // Redirect to the dashboard
            window.location.href = 'aid-dashboard.php';
        });
    }

    // Show the card on load if there's a success message
    const showCard = <?php echo isset($success_message) ? 'true' : 'false'; ?>;
    if (showCard) {
        const card = document.querySelector('.submit_aid-card');
        if (card) {
            card.classList.add('show');
        }
    }
});
