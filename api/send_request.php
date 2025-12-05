<?php
include_once 'config.php';
$data = json_decode(file_get_contents("php://input"));

if(isset($data->student_identifier) && isset($data->parent_id)) {
    try {
        $identifier = trim($data->student_identifier);
        $query = "SELECT id, email FROM users WHERE (email = :id OR id = :id OR full_name LIKE :name) AND role = 'STUDENT' LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":id", $identifier);
        $nameSearch = "%" . $identifier . "%";
        $stmt->bindParam(":name", $nameSearch);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            $studentId = $student['id'];
            
            $requestJson = json_encode([
                "fromId" => $data->parent_id,
                "fromName" => $data->parent_name,
                "type" => "PARENT_LINK",
                "date" => date('Y-m-d')
            ]);
            
            $update = $conn->prepare("UPDATE users SET pending_request_json = ? WHERE id = ?");
            if($update->execute([$requestJson, $studentId])) {
                echo json_encode(["message" => "Request Sent Successfully"]);
            } else { http_response_code(500); echo json_encode(["message" => "Failed to update student record"]); }
        } else { http_response_code(404); echo json_encode(["message" => "Student account not found with this ID/Email"]); }
    } catch(PDOException $e) { http_response_code(500); echo json_encode(["message" => "Database error: " . $e->getMessage()]); }
} else { http_response_code(400); echo json_encode(["message" => "Missing required fields"]); }