<?php
// Enable error reporting for debugging (remove or disable for production!)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';

// Query the database to get the data from the call_flags table
$sql = "SELECT id, parent_id, flag_name, display_order, specify, followup FROM call_flags";
$result = $conn->query($sql);

$todos = array();

if ($result->num_rows > 0) {
    // Fetch all rows and store them in the $todos array
    while($row = $result->fetch_assoc()) {
        $todos[] = $row;
    }
} else {
    echo "0 results";
}

// Close the database connection
$conn->close();

// Return the data as JSON
echo json_encode($todos);
?>