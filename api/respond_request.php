<?php
include_once 'config.php';
$data = json_decode(file_get_contents("php://input"));

if(isset($data->student_id) && isset($data->accept)) {
    try {
        $conn->beginTransaction();
        $clear = $conn->prepare("UPDATE users SET pending_request_json = NULL WHERE id = ?");
        $clear->execute([$data->student_id]);
        
        if ($data->accept && isset($data->parent_id)) {
            $linkS = $conn->prepare("UPDATE users SET parent_id = ? WHERE id = ?");
            $linkS->execute([$data->parent_id, $data->student_id]);
            $linkP = $conn->prepare("UPDATE users SET student_id = ? WHERE id = ?");
            $linkP->execute([$data->student_id, $data->parent_id]);
            echo json_encode(["message" => "Connection Accepted"]);
        } else { echo json_encode(["message" => "Request Declined"]); }
        
        $conn->commit();
    } catch(Exception $e) { $conn->rollBack(); http_response_code(500); echo json_encode(["message" => "Database error: " . $e->getMessage()]); }
}