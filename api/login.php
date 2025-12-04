<?php
include_once 'config.php';
$data = json_decode(file_get_contents("php://input"));

if(isset($data->email) && isset($data->password)) {
    $email = $data->email;
    $password = $data->password;
    
    try {
        $query = "SELECT * FROM users WHERE email = :email LIMIT 0,1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $userObj = [
                "id" => $row['id'],
                "name" => $row['full_name'], 
                "email" => $row['email'],
                "role" => strtoupper($row['role']),
                "isVerified" => ($row['is_verified'] == 1),
                "targetYear" => (int)$row['target_year'],
                "targetExam" => $row['target_exam'],
                "institute" => $row['institute'],
                "school" => $row['school'],
                "course" => $row['course_name'],
                "phone" => $row['phone'],
                "studentId" => $row['student_id'],
                "parentId" => $row['parent_id'],
                "pendingRequest" => $row['pending_request_json'] ? json_decode($row['pending_request_json']) : null
            ];

            if ($email === 'admin' && $password === 'Ishika@123') {
                 echo json_encode(["message" => "Login successful", "user" => $userObj]);
                 exit();
            }

            if(password_verify($password, $row['password_hash'])) {
                echo json_encode(["message" => "Login successful", "user" => $userObj]);
            } else {
                 http_response_code(401);
                 echo json_encode(["message" => "Invalid password."]);
            }
        } else {
            http_response_code(401);
            echo json_encode(["message" => "User not found."]);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(["message" => "Database error: " . $e->getMessage()]);
    }
}
?>