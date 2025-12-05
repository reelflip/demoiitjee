<?php
include_once 'config.php';
try {
    $stmt = $conn->query("SELECT id, full_name as name, email, role, is_verified, created_at, target_exam, institute FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($users as &$user) {
        $user['avatarUrl'] = "https://api.dicebear.com/7.x/avataaars/svg?seed=" . $user['email'];
        $user['isVerified'] = ($user['is_verified'] == 1);
        $user['targetExam'] = $user['target_exam'];
    }
    echo json_encode($users);
} catch(PDOException $e) { http_response_code(500); echo json_encode(["error" => $e->getMessage()]); }