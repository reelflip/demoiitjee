<?php
// api/config.php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$DB_HOST = "localhost";
$DB_USER = "YOUR_DB_USER";
$DB_PASS = "YOUR_DB_PASSWORD";
$DB_NAME = "YOUR_DB_NAME";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

$conn->set_charset("utf8mb4");

/**
 * Universal input handler
 * Supports JSON + FormData
 */
function getInput() {
    if (!empty($_POST)) {
        return $_POST;
    }

    $raw = file_get_contents("php://input");
    if (!$raw) return [];

    $json = json_decode($raw, true);
    return is_array($json) ? $json : [];
}

/**
 * Safe JSON response
 */
function jsonResponse($data, $code = 200) {
    http_response_code($code);
    header("Content-Type: application/json");
    echo json_encode($data);
    exit;
}
