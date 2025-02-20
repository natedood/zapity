<?php
// Enable error reporting for debugging (remove or disable for production!)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';

// Get the id and status from the URL parameters
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$status = isset($_GET['status']) ? intval($_GET['status']) : 0;

if ($id > 0) {
    // Prepare the SQL statement to update the todo status
    $stmt = $conn->prepare("UPDATE todos SET status = ? WHERE id = ?");
    $stmt->bind_param("ii", $status, $id);

    // Execute the statement
    if ($stmt->execute()) {
        // Fetch the updated todo item
        $stmt = $conn->prepare("SELECT * FROM todos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $todos = $result->fetch_assoc();
    } else {
        $todos = ['error' => 'Failed to update todo status'];
    }

    // Close the statement
    $stmt->close();
} else {
    $todos = ['error' => 'Invalid ID'];
}

// Close the database connection
$conn->close();

// Return the data as JSON
echo json_encode($todos);
?>