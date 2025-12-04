<?php
include_once 'config.php';
$data = json_decode(file_get_contents("php://input"));
if(isset($data->user_id) && isset($data->topic_id)) {
    $check = $conn->prepare("SELECT id FROM topic_progress WHERE user_id = ? AND topic_id = ?");
    $check->execute([$data->user_id, $data->topic_id]);
    $ex1s = $data->ex1Solved ?? 0; $ex1t = $data->ex1Total ?? 30;
    $ex2s = $data->ex2Solved ?? 0; $ex2t = $data->ex2Total ?? 20;
    $ex3s = $data->ex3Solved ?? 0; $ex3t = $data->ex3Total ?? 15;
    $ex4s = $data->ex4Solved ?? 0; $ex4t = $data->ex4Total ?? 10;
    if($check->rowCount() > 0) {
        $sql = "UPDATE topic_progress SET status=?, ex1_solved=?, ex1_total=?, ex2_solved=?, ex2_total=?, ex3_solved=?, ex3_total=?, ex4_solved=?, ex4_total=? WHERE user_id=? AND topic_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$data->status, $ex1s, $ex1t, $ex2s, $ex2t, $ex3s, $ex3t, $ex4s, $ex4t, $data->user_id, $data->topic_id]);
    } else {
        $sql = "INSERT INTO topic_progress (user_id, topic_id, status, ex1_solved, ex1_total, ex2_solved, ex2_total, ex3_solved, ex3_total, ex4_solved, ex4_total) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$data->user_id, $data->topic_id, $data->status, $ex1s, $ex1t, $ex2s, $ex2t, $ex3s, $ex3t, $ex4s, $ex4t]);
    }
    echo json_encode(["message" => "Progress saved"]);
}
?>