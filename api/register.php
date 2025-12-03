<?php
include_once 'config.php';
$data = json_decode(file_get_contents("php://input"));

if(isset($data->email) && isset($data->password)) {
    $email = $data->email;
    $password = password_hash($data->password, PASSWORD_BCRYPT);
    $name = $data->name;
    $role = $data->role;

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
        
        // Return user object so frontend can auto-login
        echo json_encode([
            "message" => "Registration successful", 
            "user" => [
                "id" => $newUserId,
                "name" => $name,
                "email" => $email,
                "role" => $role,
                "isVerified" => true
            ]
        ]);
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Error registering user. Email might be taken."]);
    }
}
?>