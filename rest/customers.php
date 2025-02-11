<?php
require_once 'db_connect.php';

function getCustomersByPhoneNumber($phoneNumber) {
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $sql = "SELECT c.* FROM customers c
            INNER JOIN customer_phones cp ON c.customer_id = cp.customer_id
            WHERE cp.phone_number = '$phoneNumber'";
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $customers = array();
        
        while ($row = $result->fetch_assoc()) {
            $customers[] = $row;
        }
        
        return $customers;
    } else {
        return null;
    }
}

// Example usage
$phoneNumber = '1111111111';
$customers = getCustomersByPhoneNumber($phoneNumber);

if ($customers) {
    echo json_encode($customers);
} else {
    echo "No customers found for the given phone number.";
}

$conn->close();
?>