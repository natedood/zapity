<?php
// Enable error reporting for debugging (remove or disable for production!)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';

// Decode the JSON data from the AJAX POST request
$data = json_decode($_POST['data'], true);
if (!$data) {
    die("Error decoding JSON: " . json_last_error_msg());
}



// Close the database connection
$conn->close();

// Return a success response
echo json_encode(['status' => 'success']);
?>