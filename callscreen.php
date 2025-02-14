<?php
include 'db_connect.php';

// Your code here
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

// Insert into the call_flags table and call_flags_specify table
foreach ($flags as $flag) {
    $flag_id = $flag['id'];
    $specify = $flag['specify'];

    // Insert into call_flags table
    $stmt = $conn->prepare("INSERT INTO call_flags (call_id, call_flag_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $call_id, $flag_id);
    $stmt->execute();
    $call_flags_link_id = $stmt->insert_id;
    $stmt->close();

    // If specify is not null, insert into call_flags_specify table
    if (!is_null($specify)) {
        $stmt = $conn->prepare("INSERT INTO call_flags_specify (call_flags_link_id, specify) VALUES (?, ?)");
        $stmt->bind_param("is", $call_flags_link_id, $specify);
        $stmt->execute();
        $stmt->close();
    }
}

// Close the database connection
$conn->close();

// Return a success response
echo json_encode(['status' => 'success']);
?>