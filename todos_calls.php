<?php
// Enable error reporting for debugging (remove or disable for production!)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';

// Get the type from the URL query parameter, default to 1 if not set
$type = isset($_GET['type']) ? intval($_GET['type']) : 1;

// Get the status from the query string, default to "0" if not provided
$statusParam = isset($_GET['status']) && !empty($_GET['status']) ? $_GET['status'] : "0";
// Sanitize status parameter (only numbers and commas allowed)
$statusParam = preg_replace('/[^0-9,]/', '', $statusParam);

// Get the start date and end date from the query string.
// If not provided, default to today's date.
$startDate = isset($_GET['startDate']) && !empty($_GET['startDate']) ? $_GET['startDate'] : date('Y-m-d');
$endDate   = isset($_GET['endDate']) && !empty($_GET['endDate']) ? $_GET['endDate'] : date('Y-m-d');

// Check if statusParam contains a 0; if so, adjust query with additional UNION
if (strpos($statusParam, '0') !== false) {
    $query = "(
        SELECT t.id, t.due_datetime, t.status,
               c.id AS call_id, c.phone_number, c.notes AS call_notes,
               (SELECT caller_id_name 
                FROM calls 
                WHERE id = c.id 
                ORDER BY call_datetime DESC 
                LIMIT 1) AS caller_id_name
        FROM todos t
        JOIN calls c ON t.link_id = c.id
        WHERE t.status IN ($statusParam)
          AND t.todo_type_id = $type
          AND DATE(t.due_datetime) BETWEEN ? AND ?
    )
    UNION
    (
        SELECT t.id, t.due_datetime, t.status,
               c.id AS call_id, c.phone_number, c.notes AS call_notes,
               (SELECT caller_id_name 
                FROM calls 
                WHERE id = c.id 
                ORDER BY call_datetime DESC 
                LIMIT 1) AS caller_id_name
        FROM todos t
        JOIN calls c ON t.link_id = c.id
        WHERE t.status = 0
          AND t.todo_type_id = $type
          AND DATE(t.due_datetime) <= ?
    )
    ORDER BY due_datetime ASC";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    // Bind only for the first part of the UNION.
    $stmt->bind_param("sss", $startDate, $endDate,$endDate);
} else {
    $query = "
    SELECT t.id, t.due_datetime, t.status,
           c.id AS call_id, c.phone_number, c.notes AS call_notes,
           (SELECT caller_id_name 
            FROM calls 
            WHERE id = c.id 
            ORDER BY call_datetime DESC 
            LIMIT 1) AS caller_id_name
    FROM todos t
    JOIN calls c ON t.link_id = c.id
    WHERE t.status IN ($statusParam)
      AND t.todo_type_id = $type
      AND DATE(t.due_datetime) BETWEEN ? AND ?
    ORDER BY t.due_datetime ASC
    ";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ss", $startDate, $endDate);
}

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

// Get the result set from the statement
$result = $stmt->get_result();
$todos = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $todos[] = $row;
    }
} else {
    $todos = array("message" => "No todos found");
}
$stmt->close();

// Return the data as JSON
echo json_encode($todos);
?>