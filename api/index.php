<?php
include_once 'config.php';
echo json_encode([
    "status" => "active", 
    "message" => "JEE Tracker API is running", 
    "timestamp" => date('c'),
    "info" => "Use endpoints like /login.php, /test_db.php etc."
]);
?>