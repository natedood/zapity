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

// Extract data from the decoded JSON
$phone_number  = $data['phone_number'];
$flags         = $data['flags'];
$followup_date = $data['followup_date'];
$call_notes    = $data['call_notes'];
$call_origin   = $data['call_origin'];

// Insert into the calls table, including the call_origin field
$call_datetime = date('Y-m-d H:i:s');
$stmt = $conn->prepare("INSERT INTO calls (phone_number, call_datetime, call_origin, notes) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    die("Prepare failed (calls): " . $conn->error);
}
$stmt->bind_param("ssss", $phone_number, $call_datetime, $call_origin, $call_notes);
if (!$stmt->execute()) {
    die("Execute failed (calls): " . $stmt->error);
}
$call_id = $stmt->insert_id;
$stmt->close();

// Loop over the flags array and insert into the calls_flags_link table
foreach ($flags as $flag) {
    $call_flag_id = $flag['id'];
    // If specify value is provided, use it; otherwise, insert null.
    $specify = isset($flag['specify']) ? $flag['specify'] : null;
    
    $stmt = $conn->prepare("INSERT INTO calls_flags_link (call_id, call_flag_id, specify) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed (calls_flags_link): " . $conn->error);
    }
    $stmt->bind_param("iis", $call_id, $call_flag_id, $specify);
    if (!$stmt->execute()) {
        die("Execute failed (calls_flags_link): " . $stmt->error);
    }
    $stmt->close();
}

// If there is a valid followup_date, insert a row into the todos table.
if (!empty($followup_date)) {
    // Set a default todo_type_id (adjust as needed; this type id is for a call follow up).
    $todo_type_id = 1;
    $stmt = $conn->prepare("INSERT INTO todos (todo_type_id, link_id, due_datetime, notes) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed (todos): " . $conn->error);
    }
    $stmt->bind_param("iiss", $todo_type_id, $call_id, $followup_date, $call_notes);
    if (!$stmt->execute()) {
        die("Execute failed (todos): " . $stmt->error);
    }
    $stmt->close();
}

// Close the database connection
$conn->close();

// Return a success response
echo json_encode(['status' => 'success']);
?>