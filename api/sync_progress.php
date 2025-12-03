<?php
include_once 'config.php';
$data = json_decode(file_get_contents("php://input"));

if(isset($data->user_id) && isset($data->topic_id)) {
    // Check if exists
    $check = $conn->prepare("SELECT id FROM topic_progress WHERE user_id = ? AND topic_id = ?");
    $check->execute([$data->user_id, $data->topic_id]);
    
    if($check->rowCount() > 0) {
        $sql = "UPDATE topic_progress SET status=?, ex1_solved=?, ex1_total=?, ex2_solved=?, ex2_total=?, ex3_solved=?, ex3_total=?, ex4_solved=?, ex4_total=? WHERE user_id=? AND topic_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $data->status, 
            $data->ex1Solved, $data->ex1Total, 
            $data->ex2Solved, $data->ex2Total, 
            $data->ex3Solved, $data->ex3Total,
            $data->ex4Solved, $data->ex4Total,
            $data->user_id, $data->topic_id
        ]);
    } else {
        $sql = "INSERT INTO topic_progress (user_id, topic_id, status, ex1_solved, ex1_total, ex2_solved, ex2_total, ex3_solved, ex3_total, ex4_solved, ex4_total) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $data->user_id, $data->topic_id, $data->status,
            $data->ex1Solved, $data->ex1Total, 
            $data->ex2Solved, $data->ex2Total, 
            $data->ex3Solved, $data->ex3Total,
            $data->ex4Solved, $data->ex4Total
        ]);
    }
    echo json_encode(["message" => "Progress saved"]);
}
?>