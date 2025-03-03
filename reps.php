<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';

// Check the request method
$method = $_SERVER['REQUEST_METHOD'];

// Handle GET request
if ($method === 'GET') {
    // Check if the 'location_id' parameter is set in the URL
    if (isset($_GET['location_id'])) {
        $location_id = $_GET['location_id'];

        // Fetch the reps associated with the given location_id
        $sql = "SELECT * FROM location_reps WHERE location_id = $location_id";
        $result = $conn->query($sql);

        // Create an array to store the reps
        $reps = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Create an associative array for each rep
                $rep = array(
                    "id" => $row["id"],
                    "location_id" => $row["location_id"],
                    "rep_name" => $row["rep_name"],
                    "phone" => $row["phone"]
                );

                // Add the rep to the array
                $reps[] = $rep;
            }
        } else {
            $reps[] = array(
                "id" => 0,
                "location_id" => $location_id,
                "rep_name" => "No reps found",
                "phone" => ""
            );
            
            //echo "No reps found for the given location.";
        }

        http_response_code(200); // OK
        
        // Set the content type to JSON
        header('Content-Type: application/json');
        echo json_encode($reps);
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'location_id parameter is required']);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Method Not Allowed']);
}

?>