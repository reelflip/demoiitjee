<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle CORS Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// DB CONFIGURATION FROM ADMIN PANEL INPUTS
$host = "82.25.121.80"; 
$db_name = "u131922718_iitjee_tracker";
$username = "u131922718_iitjee_user";
$password = "HC2>RF|J>a!9";

try {
    $conn = new PDO("mysql:host=" . $host . ";dbname=" . $db_name, $username, $password);
    $conn->exec("set names utf8mb4");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $exception) {
    http_response_code(500);
    // Use 'message' key instead of 'error' for consistency with frontend expectations
    echo json_encode(["message" => "Connection error: " . $exception->getMessage()]);
    exit();
}
?>
