<?php
include_once 'config.php';
$data = json_decode(file_get_contents("php://input"));

try {
    $conn->beginTransaction();
    
    // Save Attempt
    $stmt = $conn->prepare("INSERT INTO test_attempts (id, user_id, test_id, score, total_questions, correct_count, incorrect_count, accuracy_percent) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$data->id, $data->user_id, $data->testId, $data->score, $data->totalQuestions, $data->correctCount, $data->incorrectCount, $data->accuracy_percent]);
    
    // Save Details
    if (!empty($data->detailedResults)) {
        $stmtDetail = $conn->prepare("INSERT INTO attempt_details (attempt_id, question_id, status) VALUES (?, ?, ?)");
        foreach($data->detailedResults as $res) {
            $stmtDetail->execute([$data->id, $res->questionId, $res->status]);
        }
    }
    
    $conn->commit();
    echo json_encode(["message" => "Attempt saved"]);
} catch (Exception $e) {
    $conn->rollBack();
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>