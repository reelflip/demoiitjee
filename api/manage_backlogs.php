<?php
include_once 'config.php';
$data = json_decode(file_get_contents("php://input"));
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST') {
    $stmt = $conn->prepare("INSERT INTO backlogs (id, user_id, title, subject_id, priority, deadline, status) VALUES (?, ?, ?, ?, ?, ?, 'PENDING')");
    $stmt->execute([$data->id, $data->user_id, $data->title, $data->subjectId, $data->priority, $data->deadline]);
    echo json_encode(["message" => "Backlog added"]);
} elseif ($method === 'PUT') {
    $stmt = $conn->prepare("UPDATE backlogs SET status = IF(status='PENDING','CLEARED','PENDING') WHERE id = ?");
    $stmt->execute([$data->id]);
    echo json_encode(["message" => "Status updated"]);
} elseif ($method === 'DELETE') {
    $stmt = $conn->prepare("DELETE FROM backlogs WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    echo json_encode(["message" => "Deleted"]);
}
?>