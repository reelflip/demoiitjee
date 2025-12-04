<?php
include_once 'config.php';
$data = json_decode(file_get_contents("php://input"));

if(isset($data->student_identifier) && isset($data->parent_id)) {
    try {
        // Find Student by Email or ID
        $query = "SELECT id FROM users WHERE (email = ? OR id = ?) AND role = 'STUDENT' LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->execute([$data->student_identifier, $data->student_identifier]);
        
        if($stmt->rowCount() > 0) {
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            $studentId = $student['id'];
            
            $requestJson = json_encode([
                "fromId" => $data->parent_id,
                "fromName" => $data->parent_name,
                "type" => "PARENT_LINK"
            ]);
            
            $update = $conn->prepare("UPDATE users SET pending_request_json = ? WHERE id = ?");
            if($update->execute([$requestJson, $studentId])) {
                echo json_encode(["message" => "Request Sent"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Failed to update student"]);
            }
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Student not found"]);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(["message" => "Database error: " . $e->getMessage()]);
    }
}
?>