<?php
include_once 'config.php';
$user_id = $_GET['user_id'];
if (!$user_id) { echo json_encode(["error" => "No User ID"]); exit(); }
try {
    $userQuery = $conn->prepare("SELECT id, pending_request_json, parent_id, student_id FROM users WHERE id = ?");
    $userQuery->execute([$user_id]);
    $userData = $userQuery->fetch(PDO::FETCH_ASSOC);
    $userProfileSync = [
        "pendingRequest" => $userData['pending_request_json'] ? json_decode($userData['pending_request_json']) : null,
        "parentId" => $userData['parent_id'],
        "studentId" => $userData['student_id']
    ];

    $progQuery = $conn->prepare("SELECT * FROM topic_progress WHERE user_id = ?");
    $progQuery->execute([$user_id]);
    $progress = $progQuery->fetchAll(PDO::FETCH_ASSOC);
    
    $goalsQuery = $conn->prepare("SELECT * FROM daily_goals WHERE user_id = ? AND created_at = CURDATE()");
    $goalsQuery->execute([$user_id]);
    $goals = $goalsQuery->fetchAll(PDO::FETCH_ASSOC);
    
    $blogQuery = $conn->prepare("SELECT * FROM backlogs WHERE user_id = ?");
    $blogQuery->execute([$user_id]);
    $backlogs = $blogQuery->fetchAll(PDO::FETCH_ASSOC);
    
    $mistakeQuery = $conn->prepare("SELECT * FROM mistake_notebook WHERE user_id = ?");
    $mistakeQuery->execute([$user_id]);
    $mistakes = $mistakeQuery->fetchAll(PDO::FETCH_ASSOC);
    
    $ttQuery = $conn->prepare("SELECT config_json, generated_slots_json FROM timetable_settings WHERE user_id = ?");
    $ttQuery->execute([$user_id]);
    $timetableRow = $ttQuery->fetch(PDO::FETCH_ASSOC);
    $timetable = $timetableRow ? ["config" => json_decode($timetableRow['config_json']), "slots" => json_decode($timetableRow['generated_slots_json'])] : null;
    
    $attemptsQuery = $conn->prepare("SELECT * FROM test_attempts WHERE user_id = ? ORDER BY attempt_date DESC");
    $attemptsQuery->execute([$user_id]);
    $attempts = $attemptsQuery->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($attempts as &$attempt) {
        $detailQuery = $conn->prepare("SELECT * FROM attempt_details WHERE attempt_id = ?");
        $detailQuery->execute([$attempt['id']]);
        $details = $detailQuery->fetchAll(PDO::FETCH_ASSOC);
        $attempt['detailedResults'] = array_map(function($d) { return ["questionId" => $d['question_id'], "status" => $d['status']]; }, $details);
    }
    
    echo json_encode([ 
        "userProfileSync" => $userProfileSync,
        "progress" => $progress, 
        "goals" => $goals, 
        "backlogs" => $backlogs, 
        "mistakes" => $mistakes, 
        "timetable" => $timetable,
        "attempts" => $attempts
    ]);
} catch(Exception $e) { http_response_code(500); echo json_encode(["error" => $e->getMessage()]); }
?>