<?php
// Enable error reporting for debugging (remove or disable for production!)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';

// Get the status from the query string, default to "9" if not provided
$statusParam = isset($_GET['status']) && !empty($_GET['status']) ? $_GET['status'] : "9";
// Sanitize status parameter (only numbers and commas allowed)
$statusParam = preg_replace('/[^0-9,]/', '', $statusParam);

// Get the start date and end date from the query string.
// If not provided, default to today's date.
$startDate = isset($_GET['startDate']) && !empty($_GET['startDate']) ? $_GET['startDate'] : date('Y-m-d');
$endDate   = isset($_GET['endDate']) && !empty($_GET['endDate']) ? $_GET['endDate'] : date('Y-m-d');

// Build the SQL query to filter todos based on the date portion of due_datetime
$sql = "SELECT * FROM todos 
        WHERE status IN ($statusParam) 
        AND DATE(due_datetime) BETWEEN ? AND ? 
        ORDER BY due_datetime ASC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind the startDate and endDate parameters as strings.
$stmt->bind_param("ss", $startDate, $endDate);

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

// Get the result set from the statement
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