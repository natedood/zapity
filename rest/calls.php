<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// GET all calls
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
        exit();
    }

    // Check if "last" parameter is provided
    $last = isset($_GET['last']) ? intval($_GET['last']) : 10;
    $sql = "SELECT * FROM calls ORDER BY call_datetime DESC LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $last);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $calls = array();
        while ($row = $result->fetch_assoc()) {
            $calls[] = $row;
        }
        echo json_encode($calls);
    } else {
        echo json_encode(["message" => "No calls found."]);
    }

    $stmt->close();
    $conn->close();
}

// POST a new call
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Validate and sanitize input data
    $customer_id = $_POST['customer_id'] ?? null;
    // Add more validation and sanitization for other fields if needed

    // Insert new call into the database
    $sql = "INSERT INTO calls (customer_id) VALUES ('$customer_id')";
    if ($conn->query($sql) === TRUE) {
        echo "New call created successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}

// PUT update an existing call
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $putData);
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Validate and sanitize input data
    $id = $putData['id'] ?? null;
    $customer_id = $putData['customer_id'] ?? null;
    // Add more validation and sanitization for other fields if needed

    // Update the call in the database
    $sql = "UPDATE calls SET customer_id='$customer_id' WHERE id='$id'";
    if ($conn->query($sql) === TRUE) {
        echo "Call updated successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}

// DELETE an existing call
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $deleteData);
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Validate and sanitize input data
    $id = $deleteData['id'] ?? null;

    // Delete the call from the database
    $sql = "DELETE FROM calls WHERE id='$id'";
    if ($conn->query($sql) === TRUE) {
        echo "Call deleted successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>