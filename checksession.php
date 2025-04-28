<?php
// Set session lifetime to 16 hours
ini_set('session.gc_maxlifetime', 57600);
ini_set('session.cookie_lifetime', 57600);

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // User is not logged in, redirect to login page
    header("Location: login.php");
    exit;
}
?>