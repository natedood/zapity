<?php
// Enable error reporting for debugging (remove or disable for production!)
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Include the session check script
include 'checksession.php';
include 'db_connect.php';

// Decode the JSON data from the AJAX POST request
$data = json_decode($_POST['data'], true);
if (!$data) {
    die("Error decoding JSON: " . json_last_error_msg());
}

// Extract data from the decoded JSON
$phone_number    = $data['phone_number'];
$caller_id_name  = $data['caller_id_name'];
$flags           = $data['flags'];
$followup_date   = $data['followup_date'];
$call_notes      = $data['call_notes'];
$call_origin     = $data['call_origin'];

// New fields for closing the lead:
$close_lead    = isset($data['close_lead']) ? (int)$data['close_lead'] : 0;
$lead_result   = isset($data['lead_result']) ? (int)$data['lead_result'] : null;

// Check if clearTodo is set and equals 1
if (isset($data['clearTodo']) && $data['clearTodo'] == 1) {
    // server is in UTC, so subtract 6 hours to get the correct date in CST
    // todo: timezone should be set in php.ini
    $today = date('Y-m-d', strtotime('-6 hours'));
    $stmt = $conn->prepare("UPDATE todos t
                            JOIN calls c ON t.link_id = c.id
                            SET t.status = 1
                            WHERE t.due_datetime = ? 
                            AND c.phone_number = ?");
    if (!$stmt) {
        die("Prepare failed (update todos): " . $conn->error);
    }
    $stmt->bind_param("ss", $today, $phone_number);
    if (!$stmt->execute()) {
        die("Execute failed (update todos): " . $stmt->error);
    }
    $stmt->close();
}

// Ensure the session is started
session_start();

// Get the user ID from the session
$user_id = isset($_SESSION["id"]) ? $_SESSION["id"] : null;

// Insert into the calls table, including the call_origin field and user_id
// Adjust time by -6 hours to convert from UTC to CST
$call_datetime = date('Y-m-d H:i:s', strtotime('-5 hours'));
$stmt = $conn->prepare("INSERT INTO calls (phone_number, call_datetime, call_origin, notes, caller_id_name, user_id) VALUES (?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    die("Prepare failed (calls): " . $conn->error);
}
$stmt->bind_param("sssssi", $phone_number, $call_datetime, $call_origin, $call_notes, $caller_id_name, $user_id);
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

// If close_lead is selected, update all call records with this phone number that have lead_status_id set to 0 or NULL,
// and update all todo records for this phone number to status 1.
if ($close_lead === 1) {
    // Update calls.lead_status_id for records with lead_status_id of 0 or NULL.
    $stmt = $conn->prepare("UPDATE calls SET lead_status_id = ? WHERE phone_number = ? AND (lead_status_id IS NULL OR lead_status_id = 0)");
    if (!$stmt) {
        die("Prepare failed (update calls lead_status): " . $conn->error);
    }
    $stmt->bind_param("is", $lead_result, $phone_number);
    if (!$stmt->execute()) {
        die("Execute failed (update calls lead_status): " . $stmt->error);
    }
    $stmt->close();

    // Update all todos records for calls with this phone number to status 1.
    $stmt = $conn->prepare("UPDATE todos t
                            JOIN calls c ON t.link_id = c.id 
                            SET t.status = 1
                            WHERE c.phone_number = ?");
    if (!$stmt) {
        die("Prepare failed (update todos status): " . $conn->error);
    }
    $stmt->bind_param("s", $phone_number);
    if (!$stmt->execute()) {
        die("Execute failed (update todos status): " . $stmt->error);
    }
    $stmt->close();
}

// Close the database connection
$conn->close();

// Return a success response
echo json_encode(['status' => 'success']);
?>