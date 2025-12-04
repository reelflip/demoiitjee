<?php
include_once 'config.php';
$data = json_decode(file_get_contents("php://input"));

if(isset($data->email) && isset($data->password)) {
    $email = $data->email;
    $password = password_hash($data->password, PASSWORD_BCRYPT);
    $name = $data->name;
    $role = $data->role;

    try {
        // Check if email exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if($check->rowCount() > 0) {
            http_response_code(409);
            echo json_encode(["message" => "Email already exists. Please login."]);
            exit();
        }

        // is_verified set to 1 by default, no token needed
        $query = "INSERT INTO users (email, password_hash, full_name, role, is_verified, institute, target_year) VALUES (:email, :pass, :name, :role, 1, :inst, :year)";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":pass", $password);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":inst", $data->institute);
        $stmt->bindParam(":year", $data->targetYear);

        if($stmt->execute()) {
            $newUserId = $conn->lastInsertId();
            
            // Return success with Normalized Data
            echo json_encode([
                "message" => "Registration successful", 
                "user" => [
                    "id" => $newUserId,
                    "name" => $name,
                    "email" => $email,
                    "role" => $role,
                    "isVerified" => true,
                    "institute" => $data->institute,
                    "targetYear" => $data->targetYear
                ]
            ]);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Error registering user."]);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(["message" => "Database error: " . $e->getMessage()]);
    }
}
?>