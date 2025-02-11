
<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Database connection
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// GET all customer phones
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if "last" parameter is provided
    $last = isset($_GET['last']) ? intval($_GET['last']) : 10;
    $sql = "SELECT * FROM customer_phones ";

    if (isset($_GET['phone_number'])) {
        $phone_number = $_GET['phone_number'];
        $sql .= " WHERE phone_number = ?";
        $stmt->bind_param("s", $phone_number);
    }
    $sql = " LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $last);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $phones = array();
        while ($row = $result->fetch_assoc()) {
            $phones[] = $row;
        }
        echo json_encode($phones);
    } else {
        echo json_encode(["message" => "No customer phones found."]);
    }

    $stmt->close();
}

// POST a new customer phone
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input data
    $customer_id = $_POST['customer_id'] ?? null;
    $phone_number = $_POST['phone_number'] ?? null;
    $phone_type = $_POST['phone_type'] ?? null;

    if ($customer_id && $phone_number && $phone_type) {
        // Insert new customer phone into the database
        $sql = "INSERT INTO customer_phones (customer_id, phone_number, phone_type) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $customer_id, $phone_number, $phone_type);

        if ($stmt->execute()) {
            echo json_encode(["message" => "New customer phone created successfully."]);
        } else {
            echo json_encode(["error" => "Error: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["error" => "Invalid input data."]);
    }
}

// PUT update an existing customer phone
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $putData);

    // Validate and sanitize input data
    $phone_id = $putData['phone_id'] ?? null;
    $customer_id = $putData['customer_id'] ?? null;
    $phone_number = $putData['phone_number'] ?? null;
    $phone_type = $putData['phone_type'] ?? null;

    if ($phone_id && $customer_id && $phone_number && $phone_type) {
        // Update the customer phone in the database
        $sql = "UPDATE customer_phones SET customer_id=?, phone_number=?, phone_type=? WHERE phone_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issi", $customer_id, $phone_number, $phone_type, $phone_id);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Customer phone updated successfully."]);
        } else {
            echo json_encode(["error" => "Error: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["error" => "Invalid input data."]);
    }
}

// DELETE an existing customer phone
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $deleteData);

    // Validate and sanitize input data
    $phone_id = $deleteData['phone_id'] ?? null;

    if ($phone_id) {
        // Delete the customer phone from the database
        $sql = "DELETE FROM customer_phones WHERE phone_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $phone_id);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Customer phone deleted successfully."]);
        } else {
            echo json_encode(["error" => "Error: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["error" => "Invalid input data."]);
    }
}

$conn->close();
?>
