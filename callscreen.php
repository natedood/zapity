<?php
include 'db_connect.php';

// Decode the JSON data from the AJAX POST request
$data = json_decode($_POST['data'], true);
 
// Extract data from the decoded JSON
$phone_number = $data['phone_number'];
$flags = $data['flags'];
$followup_date = $data['followup_date'];
$call_notes = $data['call_notes'];

// Insert into the calls table
$call_datetime = date('Y-m-d H:i:s');
$stmt = $conn->prepare("INSERT INTO calls (phone_number, call_datetime, notes) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $phone_number, $call_datetime, $call_notes);
$stmt->execute();
$call_id = $stmt->insert_id;
$stmt->close();

// Loop over the flags array and insert into the calls_flags_link table
foreach ($flags as $flag) {
    $call_flag_id = $flag['id'];
    $stmt = $conn->prepare("INSERT INTO calls_flags_link (call_id, call_flag_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $call_id, $call_flag_id);
    $stmt->execute();
    $stmt->close();
}

// Close the database connection
$conn->close();

// Return a success response
echo json_encode(['status' => 'success']);
?>