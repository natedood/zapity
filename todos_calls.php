<?php
// Enable error reporting for debugging (remove or disable for production!)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';

// Get the status from the URL query parameter, default to 0 if not set
$status = isset($_GET['status']) ? intval($_GET['status']) : 0;

// Get the type from the URL query parameter, default to 1 if not set
$type = isset($_GET['type']) ? intval($_GET['type']) : 1;

// Query the database
$query = "
    SELECT t.id, t.todo_type_id, t.link_id, t.notes, t.created, t.updated, t.status, t.assigned_user_id, t.due_datetime,
           c.id AS call_id, c.call_origin, c.phone_number, c.caller_id_name, c.call_datetime, c.customer_id, c.notes AS call_notes, c.message
    FROM todos t
    JOIN calls c ON t.link_id = c.id
    WHERE t.status = $status
      AND t.todo_type_id = $type
      AND t.due_datetime <= CURRENT_DATE
    ORDER BY t.due_datetime
";

$result = $conn->query($query);

$todos = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $todos[] = $row;
    }
} else {
    $todos = array("message" => "No todos found");
}

// Return the data as JSON
echo json_encode($todos);
?>