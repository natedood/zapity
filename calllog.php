<?php
// Enable error reporting for debugging (remove or disable for production!)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';

// Get the startDate and endDate from the query string (default to today if not provided)
$startDate = isset($_GET['startDate']) && !empty($_GET['startDate']) ? $_GET['startDate'] : date('Y-m-d');
$endDate   = isset($_GET['endDate'])   && !empty($_GET['endDate'])   ? $_GET['endDate']   : date('Y-m-d');

// Get the origin parameter, expected as a comma-delimited string (e.g. "in,out,walkin")
// If not provided or empty, no filtering on origin will be applied
$originParam = isset($_GET['origin']) && !empty($_GET['origin']) ? $_GET['origin'] : '';

// Get the followuponly parameter; default to 0 if not provided.
$followUpOnly = isset($_GET['followuponly']) && $_GET['followuponly'] == '1' ? 1 : 0;

// Start building the SQL query.
// We join calls_flags_link and call_flags to get the flag names, then GROUP_CONCAT them.
$sql = "SELECT calls.*, 
               GROUP_CONCAT(cf.flag_name ORDER BY cf.display_order SEPARATOR ', ') AS flags
        FROM calls 
        LEFT JOIN calls_flags_link cfl ON calls.id = cfl.call_id
        LEFT JOIN call_flags cf ON cfl.call_flag_id = cf.id
        WHERE DATE(call_datetime) BETWEEN ? AND ? ";

// If an origin filter is provided, process the comma-delimited values.
if ($originParam != '') {
    $origins = explode(',', $originParam);
    $allowedOrigins = array('in','out','walkin','textin','textout');
    $originList = [];
    foreach ($origins as $orig) {
        $orig = trim($orig);
        if (in_array($orig, $allowedOrigins)) {
            $originList[] = "'" . $conn->real_escape_string($orig) . "'";
        }
    }
    if (count($originList) > 0) {
        $sql .= " AND call_origin IN (" . implode(',', $originList) . ") ";
    }
}

// If followuponly is 1, add a condition that ensures the call has at least one flag with followup = 1.
if ($followUpOnly == 1) {
    $sql .= " AND EXISTS (
                SELECT 1 
                FROM calls_flags_link cfl2 
                JOIN call_flags cf2 ON cfl2.call_flag_id = cf2.id 
                WHERE cfl2.call_id = calls.id AND cf2.followup = 1
              ) ";
}

// Group by the call to allow aggregation of the flags and order descending by the call_datetime.
$sql .= " GROUP BY calls.id 
          ORDER BY call_datetime DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind the startDate and endDate parameters as strings.
$stmt->bind_param("ss", $startDate, $endDate);

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

// Get the result set from the statement.
$result = $stmt->get_result();
$calls = [];
while ($row = $result->fetch_assoc()) {
    $calls[] = $row;
}
$stmt->close();
$conn->close();

// Return the data as JSON.
echo json_encode($calls);
?>