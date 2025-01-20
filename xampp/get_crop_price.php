<?php
// Include database configuration
require_once 'config.php';

// Set headers for JSON response
header('Content-Type: application/json');

try {
    // Read the incoming data (JSON body)
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    // Validate JSON data
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON data");
    }

    // Extract and sanitize city and crop
    $city = isset($data['city']) ? $conn->real_escape_string($data['city']) : '';
    $crop = isset($data['crop']) ? $conn->real_escape_string($data['crop']) : '';

    // Validate input
    if (empty($city) || empty($crop)) {
        throw new Exception("Please provide both city and crop.");
    }

    // Simple query to check if the tables exist
    $tableCheckQuery = "
        SELECT 1 
        FROM information_schema.tables 
        WHERE table_schema = 'price' 
        AND table_name IN ('crops', 'cities', 'crop_prices')";
    
    $tableCheck = $conn->query($tableCheckQuery);
    
    if ($tableCheck->num_rows < 3) {
        // If tables don't exist, use a simpler query structure
        $query = "SELECT price FROM crop_prices WHERE city = ? AND crop = ? LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $city, $crop);
    } else {
        // Use the original query with joins if tables exist
        $query = "
            SELECT c.crop_name, ci.city_name, cp.price
            FROM crop_prices cp
            JOIN crops c ON cp.crop_id = c.crop_id
            JOIN cities ci ON cp.city_id = ci.city_id
            WHERE ci.city_name = ? AND c.crop_name = ?
            LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $city, $crop);
    }

    // Execute query
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            "success" => true,
            "city" => $city,
            "crop" => $crop,
            "price" => isset($row['price']) ? $row['price'] : $row[0]
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "No price data available for $crop in $city."
        ]);
    }

    $stmt->close();

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>