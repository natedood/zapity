<?php
// Get the current file name
$current_file = basename($_SERVER['SCRIPT_NAME']);

// List of files to exclude from session checking
$excluded_files = ['login.php', 'logout.php', 'hash.php'];

// Only include checksession.php if the current file is not in the excluded list
if (!in_array($current_file, $excluded_files)) {
    require_once 'checksession.php';
}

// You can include other common elements here (navigation, etc.)
?>

<script>
// Countdown timer in seconds (2 minutes = 120 seconds)
let countdown = 7200;

// Function to update the countdown timer
function updateCountdown() {
    countdown--;

    // If countdown reaches 0, redirect to login page
    if (countdown <= 0) {
        window.location.href = 'logout.php';
    }
}

// Update the countdown every second
setInterval(updateCountdown, 1000);
</script>