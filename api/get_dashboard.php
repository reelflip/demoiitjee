<?php
include_once 'config.php';
$user_id = $_GET['user_id'];

// Get Progress
$progQuery = $conn->prepare("SELECT * FROM topic_progress WHERE user_id = ?");
$progQuery->execute([$user_id]);
$progress = $progQuery->fetchAll(PDO::FETCH_ASSOC);

// Get Goals
$goalsQuery = $conn->prepare("SELECT * FROM daily_goals WHERE user_id = ? AND created_at = CURDATE()");
$goalsQuery->execute([$user_id]);
$goals = $goalsQuery->fetchAll(PDO::FETCH_ASSOC);

// Get Backlogs
$blogQuery = $conn->prepare("SELECT * FROM backlogs WHERE user_id = ?");
$blogQuery->execute([$user_id]);
$backlogs = $blogQuery->fetchAll(PDO::FETCH_ASSOC);

// Get Mistakes
$mistakeQuery = $conn->prepare("SELECT * FROM mistake_notebook WHERE user_id = ?");
$mistakeQuery->execute([$user_id]);
$mistakes = $mistakeQuery->fetchAll(PDO::FETCH_ASSOC);

// Get Timetable
$ttQuery = $conn->prepare("SELECT generated_slots_json FROM timetable_settings WHERE user_id = ?");
$ttQuery->execute([$user_id]);
$timetable = $ttQuery->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    "progress" => $progress,
    "goals" => $goals,
    "backlogs" => $backlogs,
    "mistakes" => $mistakes,
    "timetable" => $timetable ? json_decode($timetable['generated_slots_json']) : null
]);
?>