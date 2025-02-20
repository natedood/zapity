<?php
// Enable error reporting for debugging (remove or disable for production!)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';

// Get the 'number' query parameter
if (!isset($_GET['number']) || empty($_GET['number'])) {
    die("No phone number provided.");
}
$phone_number = $_GET['number'];

// Prepare the SQL query with the phone number parameter
$sql = "SELECT c.id, c.call_origin, c.phone_number, c.caller_id_name, c.call_datetime, c.customer_id, c.notes, c.message,
    cf.flag_name, cf.display_order, cf.specify, cf.followup, cfl.specify AS flag_specify
FROM calls c
JOIN calls_flags_link cfl ON c.id = cfl.call_id
JOIN call_flags cf ON cfl.call_flag_id = cf.id
WHERE c.phone_number = ?
ORDER BY call_datetime DESC, c.id DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind the phone number parameter
$stmt->bind_param("s", $phone_number);

// Execute the query
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();
$rows = array();
while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    if (!isset($rows[$id])) {
        $rows[$id] = array(
            'id' => $row['id'],
            'call_origin' => $row['call_origin'],
            'phone_number' => $row['phone_number'],
            'caller_id_name' => $row['caller_id_name'],
            'call_datetime' => $row['call_datetime'],
            'customer_id' => $row['customer_id'],
            'notes' => $row['notes'],
            'message' => $row['message'],
            'flags' => array()
        );
    }
    $rows[$id]['flags'][] = array(
        'flag_name' => $row['flag_name'],
        'display_order' => $row['display_order'],
        'specify' => $row['specify'],
        'followup' => $row['followup'],
        'flag_specify' => $row['flag_specify']
    );
}
$rows = array_values($rows); // Re-index the array to remove gaps in keys

$stmt->close();
$conn->close();

// Return the results as JSON
echo json_encode($rows);
?>