<?php
include_once 'config.php';
$data = json_decode(file_get_contents("php://input"));
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST') {
    $stmt = $conn->prepare("INSERT INTO mistake_notebook (id, user_id, question_text, subject_id, topic_id, test_name, user_notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$data->id, $data->user_id, $data->questionText, $data->subjectId, $data->topicId, $data->testName, $data->userNotes]);
} elseif ($method === 'PUT') {
    $stmt = $conn->prepare("UPDATE mistake_notebook SET user_notes = ?, tags_json = ? WHERE id = ?");
    $stmt->execute([$data->userNotes, json_encode($data->tags), $data->id]);
} elseif ($method === 'DELETE') {
    $stmt = $conn->prepare("DELETE FROM mistake_notebook WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}
?>