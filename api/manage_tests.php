<?php
include_once 'config.php';
$data = json_decode(file_get_contents("php://input"));

if (isset($data->action) && $data->action === 'add_question') {
    try {
        $stmt = $conn->prepare("INSERT INTO questions (id, subject_id, topic_id, question_text, options_json, correct_option_index) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data->id, $data->subjectId, $data->topicId, $data->text, json_encode($data->options), $data->correctOptionIndex]);
        echo json_encode(["message" => "Question added"]);
    } catch (Exception $e) { http_response_code(500); echo json_encode(["error" => $e->getMessage()]); }
}

if (isset($data->action) && $data->action === 'create_test') {
    try {
        $conn->beginTransaction();
        $stmt = $conn->prepare("INSERT INTO tests (id, title, duration_minutes, category, difficulty, exam_type) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data->id, $data->title, $data->durationMinutes, $data->category, $data->difficulty, $data->examType ?? 'JEE']);
        
        if(!empty($data->questions)) {
            $linkStmt = $conn->prepare("INSERT INTO test_questions (test_id, question_id, question_order) VALUES (?, ?, ?)");
            foreach($data->questions as $idx => $q) {
                $linkStmt->execute([$data->id, $q->id, $idx]);
            }
        }
        $conn->commit();
        echo json_encode(["message" => "Test created"]);
    } catch (Exception $e) { $conn->rollBack(); http_response_code(500); echo json_encode(["error" => $e->getMessage()]); }
}
?>