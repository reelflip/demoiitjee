<?php
include_once 'config.php';
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set PDO to throw exceptions
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$rawInput = file_get_contents("php://input");
error_log("Raw input: " . $rawInput); // Check your PHP error log

$data = json_decode($rawInput);

// Debug: Log what was received
error_log("Decoded data: " . print_r($data, true));

if(!isset($data->email) || !isset($data->password)) {
    http_response_code(400);
    echo json_encode(["message" => "Email or password missing", "received" => $data]);
    exit;
}

try {
    $email = $data->email;
    $password = password_hash($data->password, PASSWORD_BCRYPT);
    $name = $data->name ?? null;
    $role = $data->role ?? null;
    $institute = $data->institute ?? null;
    $targetYear = $data->targetYear ?? null;
    
    // Check for null values
    if(empty($name) || empty($role)) {
        http_response_code(400);
        echo json_encode(["message" => "Name or role missing"]);
        exit;
    }
    
    $query = "INSERT INTO users (email, password_hash, full_name, role, is_verified, institute, target_year) 
              VALUES (:email, :pass, :name, :role, 1, :inst, :year)";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":pass", $password);
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":role", $role);
    $stmt->bindParam(":inst", $institute);
    $stmt->bindParam(":year", $targetYear);
    
    if($stmt->execute()) {
        $newUserId = $conn->lastInsertId();
        
        http_response_code(201);
        echo json_encode([
            "message" => "Registration successful", 
            "user" => [
                "id" => $newUserId,
                "name" => $name,
                "email" => $email,
                "role" => $role,
                "isVerified" => true
            ]
        ]);
    } else {
        $errorInfo = $stmt->errorInfo();
        error_log("Execute failed: " . print_r($errorInfo, true));
        http_response_code(400);
        echo json_encode(["message" => "Error executing query", "error" => $errorInfo]);
    }
} catch(Exception $e) {
    error_log("Exception: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["message" => "Error: " . $e->getMessage()]);
}
?>
