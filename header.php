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
// Countdown timer in seconds (2 hours = 7200 seconds)
let countdown = 1800;

// Store the start time in localStorage to persist across browser sessions
if (!localStorage.getItem('logoutStartTime')) {
    localStorage.setItem('logoutStartTime', Date.now());
}

// Function to update the countdown timer
function updateCountdown() {
    // Calculate the elapsed time in seconds
    const startTime = parseInt(localStorage.getItem('logoutStartTime'), 10);
    const elapsedTime = Math.floor((Date.now() - startTime) / 1000);

    // Calculate the remaining time
    const remainingTime = countdown - elapsedTime;

    // If countdown reaches 0, redirect to the logout page
    if (remainingTime <= 0) {
        localStorage.removeItem('logoutStartTime'); // Clear the stored time
        window.location.href = 'logout.php';
    } else {
        // Optionally, update a visible countdown timer on the page
        const hours = Math.floor(remainingTime / 3600);
        const minutes = Math.floor((remainingTime % 3600) / 60);
        const seconds = remainingTime % 60;
        console.log(`Time remaining: ${hours}h ${minutes}m ${seconds}s`);
    }
}

// Use `requestAnimationFrame` to ensure the countdown updates even in throttled tabs
function startCountdown() {
    updateCountdown();
    requestAnimationFrame(startCountdown);
}

// Start the countdown
startCountdown();
</script>