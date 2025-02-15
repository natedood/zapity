<?php
// Enable error reporting for debugging (remove or disable for production!)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';

// Get the status from the query string, default to 0 if not provided
$status = isset($_GET['status']) ? intval($_GET['status']) : 0;

// Get the scope from the query string, default to 1 if not provided
$scope = isset($_GET['scope']) ? intval($_GET['scope']) : 1;

// Get the start date from the query string, default to today's date if not provided
$start = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d');

// Prepare the SQL statement based on the scope
if ($scope == 1) {
    // Only return todo objects where due_datetime is today's date
    $stmt = $conn->prepare("SELECT * FROM todos WHERE status = ? AND DATE(due_datetime) = ? ORDER BY due_datetime ASC");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("is", $status, $start);
} else {
    // Return the last X number of days worth of items
    $end_date = date('Y-m-d', strtotime($start . ' + ' . $scope . ' days'));
    $stmt = $conn->prepare("SELECT * FROM todos WHERE status = ? AND due_datetime BETWEEN ? AND ? ORDER BY due_datetime ASC");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("iss", $status, $start, $end_date);
}

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