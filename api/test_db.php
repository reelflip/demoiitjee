<?php
include_once 'config.php';
$response = [];
try {
    $response['status'] = 'CONNECTED';
    $response['db_name'] = $db_name;
    $tables = [];
    $stmt = $conn->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $table = $row[0];
        $count = $conn->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        $tables[] = ["name" => $table, "rows" => $count];
    }
    $response['tables'] = $tables;
} catch(Exception $e) {
    $response['status'] = 'ERROR';
    $response['message'] = $e->getMessage();
}
echo json_encode($response);