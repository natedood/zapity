<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';


// Check the request method
$method = $_SERVER['REQUEST_METHOD'];

// Handle GET request
if ($method === 'GET') {
    // Your code to handle GET request goes here
    
    // Fetch the contents of the locations table
    $sql = "SELECT * FROM locations";

    // Check if the 'id' parameter is set in the URL
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Use the 'id' parameter in the SQL query
        $sql .= " WHERE id = $id";
    }
    if (isset($_GET['alphasort'])) {
        $sql .= " ORDER BY location_name ASC";
    }
    // Check if 'lat' and 'lon' parameters are set in the URL
    if (isset($_GET['lat']) && isset($_GET['lon'])) {
        $lat = $_GET['lat'];
        $lon = $_GET['lon'];
        $distance = 20; // distance in miles

        // Haversine formula to calculate distance
        // add the following to narrow down total distance
        // // HAVING
            //     distance < $distance
        $sql = "
            SELECT
                id,
                location_name,
                address1,
                address2,
                city,
                state,
                zipcode,
                gps_lat,
                gps_long,
                (3959 * ACOS(COS(RADIANS($lat)) * COS(RADIANS(gps_lat)) * COS(RADIANS(gps_long) - RADIANS($lon)) + SIN(RADIANS($lat)) * SIN(RADIANS(gps_lat)))) AS distance
            FROM
                locations
            
            ORDER BY
                distance
        ";
    }

    $result = $conn->query($sql);

    // Create an array to store the locations
    $locations = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Create an associative array for each location
            $location = array(
                "id" => $row["id"],
                "location_name" => $row["location_name"],
                "address" => $row["address1"],
                "address2" => $row["address2"],
                "city" => $row["city"],
                "state" => $row["state"],
                "zipcode" => $row["zipcode"],
                "gps_latitude" => $row["gps_lat"],
                "gps_longitude" => $row["gps_long"]
            );

            // Add the location to the array
            $locations[] = $location;
        }
    } else {
        echo "No locations found.";
    }

    http_response_code(200); // OK
    
    // Set the content type to JSON
    header('Content-Type: application/json');
    echo json_encode($locations);
}

// Handle POST request
elseif ($method === 'POST') {
    // Your code to handle POST request goes here
    // ...
    // Example: Create a new location
    $data = json_decode(file_get_contents('php://input'), true);
    // Validate and process the data
    // ...
    // Example: Return the created location
    $location = ['id' => 4, 'name' => 'Location 4'];
    http_response_code(201); // Created
    echo json_encode($location);
}

// Handle other request methods
else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Method Not Allowed']);
}

?>