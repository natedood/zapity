<?php
// Enable error reporting for debugging (remove or disable for production!)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';

// Get the status from the query string, default to 0 if not provided
$status = isset($_GET['status']) ? intval($_GET['status']) : 0;

// Prepare the SQL statement to select from the todos table
$stmt = $conn->prepare("SELECT * FROM todos WHERE status = ? ORDER BY due_datetime ASC");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $status);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

// Get the result set from the executed statement
$result = $stmt->get_result();
$todos = [];
while ($row = $result->fetch_assoc()) {
    $todos[] = $row;
}
$stmt->close();

// Close the database connection
$conn->close();

// Return the data as JSON
echo json_encode($todos);
?>