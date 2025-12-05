<?php
require 'config.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->user_id) || !isset($data->topic_id)) {
    http_response_code(400);
    echo json_encode(["message" => "Missing data"]);
    exit();
}

try {
    // Check if entry exists
    $check = $conn->prepare("SELECT id FROM topic_progress WHERE user_id = :uid AND topic_id = :tid");
    $check->execute([':uid' => $data->user_id, ':tid' => $data->topic_id]);
    
    if ($check->rowCount() > 0) {
        // Update
        $sql = "UPDATE topic_progress SET 
                status = :status, 
                ex1_solved = :ex1s, ex1_total = :ex1t,
                ex2_solved = :ex2s, ex2_total = :ex2t,
                ex3_solved = :ex3s, ex3_total = :ex3t,
                ex4_solved = :ex4s, ex4_total = :ex4t,
                revision_count = :revCount, next_revision_date = :nextRev
                WHERE user_id = :uid AND topic_id = :tid";
    } else {
        // Insert
        $sql = "INSERT INTO topic_progress (user_id, topic_id, status, ex1_solved, ex1_total, ex2_solved, ex2_total, ex3_solved, ex3_total, ex4_solved, ex4_total, revision_count, next_revision_date) 
                VALUES (:uid, :tid, :status, :ex1s, :ex1t, :ex2s, :ex2t, :ex3s, :ex3t, :ex4s, :ex4t, :revCount, :nextRev)";
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':uid' => $data->user_id,
        ':tid' => $data->topic_id,
        ':status' => $data->status,
        ':ex1s' => $data->ex1Solved, ':ex1t' => $data->ex1Total,
        ':ex2s' => $data->ex2Solved, ':ex2t' => $data->ex2Total,
        ':ex3s' => $data->ex3Solved, ':ex3t' => $data->ex3Total,
        ':ex4s' => $data->ex4Solved, ':ex4t' => $data->ex4Total,
        ':revCount' => isset($data->revisionCount) ? $data->revisionCount : 0,
        ':nextRev' => isset($data->nextRevisionDate) ? $data->nextRevisionDate : null
    ]);

    echo json_encode(["message" => "Progress saved"]);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Save failed", "message" => $e->getMessage()]);
}