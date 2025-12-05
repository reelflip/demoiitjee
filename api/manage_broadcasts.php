<?php
include_once 'config.php';
$data = json_decode(file_get_contents("php://input"));
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    if ($data->action === 'send_notification') {
        try {
            $stmt = $conn->prepare("INSERT INTO notifications (id, title, message, type, created_at) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$data->id, $data->title, $data->message, $data->type, $data->date]);
            echo json_encode(["message" => "Notification sent"]);
        } catch (Exception $e) { http_response_code(500); echo json_encode(["error" => $e->getMessage()]); }
    }
    if ($data->action === 'add_quote') {
        try {
            $stmt = $conn->prepare("INSERT INTO quotes (id, text, author) VALUES (?, ?, ?)");
            $stmt->execute([$data->id, $data->text, $data->author]);
            echo json_encode(["message" => "Quote added"]);
        } catch (Exception $e) { http_response_code(500); echo json_encode(["error" => $e->getMessage()]); }
    }
} elseif ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete_quote') {
    try {
        $stmt = $conn->prepare("DELETE FROM quotes WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        echo json_encode(["message" => "Quote deleted"]);
    } catch (Exception $e) { http_response_code(500); echo json_encode(["error" => $e->getMessage()]); }
}