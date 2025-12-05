<?php
include_once 'config.php';
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"));

if ($method === 'PUT' && isset($data->id)) {
    try {
        if (isset($data->isVerified)) {
            $stmt = $conn->prepare("UPDATE users SET is_verified = ? WHERE id = ?");
            $stmt->execute([$data->isVerified ? 1 : 0, $data->id]);
            echo json_encode(["message" => "User status updated"]);
        } else {
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, role = ?, target_exam = ?, institute = ? WHERE id = ?");
            $stmt->execute([$data->name, $data->email, $data->role, $data->targetExam, $data->institute, $data->id]);
            echo json_encode(["message" => "User updated"]);
        }
    } catch (PDOException $e) { http_response_code(500); echo json_encode(["error" => $e->getMessage()]); }
} elseif ($method === 'DELETE' && isset($_GET['id'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        echo json_encode(["message" => "User deleted"]);
    } catch (PDOException $e) { http_response_code(500); echo json_encode(["error" => $e->getMessage()]); }
} else { http_response_code(405); echo json_encode(["error" => "Method not allowed"]); }