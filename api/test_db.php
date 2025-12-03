<?php
include_once 'config.php';

$response = [];

try {
    // 1. Check Connection
    $response['status'] = 'CONNECTED';
    $response['db_name'] = $db_name;
    $response['server_info'] = $conn->getAttribute(PDO::ATTR_SERVER_INFO);

    // 2. Get Table Stats
    $tables = [];
    $stmt = $conn->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $table = $row[0];
        $count = $conn->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        $tables[] = ["name" => $table, "rows" => $count];
    }
    $response['tables'] = $tables;

} catch(PDOException $e) {
    $response['status'] = 'ERROR';
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>