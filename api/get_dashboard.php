<?php
require 'config.php';
header('Content-Type: application/json');

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if (!$user_id) {
    http_response_code(400);
    echo json_encode(["message" => "User ID required"]);
    exit();
}

try {
    $response = [];

    // 1. Fetch Profile Sync (Latest Connection Status)
    $stmt = $conn->prepare("SELECT parent_id, student_id, pending_request_json FROM users WHERE id = :uid");
    $stmt->execute([':uid' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if($user) {
        $response['userProfileSync'] = [
            'parentId' => $user['parent_id'],
            'studentId' => $user['student_id'],
            'pendingRequest' => json_decode($user['pending_request_json'])
        ];
    }

    // 2. Fetch Progress
    $stmt = $conn->prepare("SELECT * FROM topic_progress WHERE user_id = :uid");
    $stmt->execute([':uid' => $user_id]);
    $response['progress'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Fetch Goals
    $stmt = $conn->prepare("SELECT * FROM daily_goals WHERE user_id = :uid");
    $stmt->execute([':uid' => $user_id]);
    $response['goals'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Fetch Backlogs
    $stmt = $conn->prepare("SELECT * FROM backlogs WHERE user_id = :uid");
    $stmt->execute([':uid' => $user_id]);
    $response['backlogs'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 5. Fetch Mistakes
    $stmt = $conn->prepare("SELECT * FROM mistake_notebook WHERE user_id = :uid ORDER BY created_at DESC");
    $stmt->execute([':uid' => $user_id]);
    $response['mistakes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 6. Fetch Timetable
    $stmt = $conn->prepare("SELECT config_json, generated_slots_json FROM timetable_settings WHERE user_id = :uid");
    $stmt->execute([':uid' => $user_id]);
    $tt = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($tt) {
        $response['timetable'] = [
            'config' => json_decode($tt['config_json']),
            'slots' => json_decode($tt['generated_slots_json'])
        ];
    }

    // 7. Fetch Test Attempts (With Details joined with Questions for Analytics)
    $stmt = $conn->prepare("SELECT * FROM test_attempts WHERE user_id = :uid ORDER BY attempt_date DESC");
    $stmt->execute([':uid' => $user_id]);
    $attempts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($attempts as &$att) {
        // JOIN to get Subject/Topic ID
        $stmtDetails = $conn->prepare("
            SELECT ad.question_id, ad.status, ad.selected_option, q.subject_id, q.topic_id 
            FROM attempt_details ad
            LEFT JOIN questions q ON ad.question_id = q.id
            WHERE ad.attempt_id = :aid
        ");
        $stmtDetails->execute([':aid' => $att['id']]);
        $details = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);
        
        // Map to frontend expectation
        $att['detailedResults'] = array_map(function($d) {
            return [
                'questionId' => $d['question_id'],
                'status' => $d['status'],
                'subjectId' => $d['subject_id'], // Critical for Analytics
                'topicId' => $d['topic_id'],
                'selectedOptionIndex' => isset($d['selected_option']) ? (int)$d['selected_option'] : null
            ];
        }, $details);
    }
    $response['attempts'] = $attempts;

    echo json_encode($response);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Fetch failed", "message" => $e->getMessage()]);
}