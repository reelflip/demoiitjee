<?php
include_once 'config.php';
$user_id = $_GET['user_id'];
if (!$user_id) { echo json_encode(["error" => "No User ID"]); exit(); }
try {
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
    $ttQuery = $conn->prepare("SELECT generated_slots_json FROM timetable_settings WHERE user_id = ?");
    $ttQuery->execute([$user_id]);
    $timetable = $ttQuery->fetch(PDO::FETCH_ASSOC);
    $attemptsQuery = $conn->prepare("SELECT * FROM test_attempts WHERE user_id = ? ORDER BY attempt_date DESC");
    $attemptsQuery->execute([$user_id]);
    $attempts = $attemptsQuery->fetchAll(PDO::FETCH_ASSOC);
    // Fetch detailed results for attempts (optional, can be separate API for speed)
    foreach($attempts as &$attempt) {
        $detailQuery = $conn->prepare("SELECT * FROM attempt_details WHERE attempt_id = ?");
        $detailQuery->execute([$attempt['id']]);
        $details = $detailQuery->fetchAll(PDO::FETCH_ASSOC);
        // Map details back to frontend structure
        $attempt['detailedResults'] = array_map(function($d) {
            return [
                "questionId" => $d['question_id'],
                "status" => $d['status']
            ];
        }, $details);
    }
    echo json_encode([ 
        "progress" => $progress, 
        "goals" => $goals, 
        "backlogs" => $backlogs, 
        "mistakes" => $mistakes, 
        "timetable" => $timetable ? json_decode($timetable['generated_slots_json']) : null,
        "attempts" => $attempts
    ]);
} catch(Exception $e) { http_response_code(500); echo json_encode(["error" => $e->getMessage()]); }
?>