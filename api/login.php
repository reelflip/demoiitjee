<?php
include_once 'config.php';
$data = json_decode(file_get_contents("php://input"));

if(isset($data->email) && isset($data->password)) {
    $email = $data->email;
    $password = $data->password;
    
    $query = "SELECT * FROM users WHERE email = :email LIMIT 0,1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    if($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Admin Override check (For recovery, can be removed in prod)
        if ($email === 'admin' && $password === 'Ishika@123') {
             unset($row['password_hash']);
             echo json_encode(["message" => "Login successful", "user" => $row]);
             exit();
        }

        if(password_verify($password, $row['password_hash'])) {
            unset($row['password_hash']);
            echo json_encode(["message" => "Login successful", "user" => $row]);
        } else {
             http_response_code(401);
             echo json_encode(["message" => "Invalid password."]);
        }
    } else {
        http_response_code(401);
        echo json_encode(["message" => "User not found."]);
    }
}
?>