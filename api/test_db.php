<?php require 'config.php'; header('Content-Type: application/json'); 
            $tables = [];
            try {
                $res = $conn->query("SHOW TABLES");
                while($row = $res->fetch(PDO::FETCH_NUM)) {
                    $tName = $row[0];
                    $count = $conn->query("SELECT COUNT(*) FROM $tName")->fetchColumn();
                    $tables[] = ["name" => $tName, "rows" => $count];
                }
                echo json_encode([
                    "status"=>"CONNECTED", 
                    "tables"=>$tables,
                    "php_version"=>phpversion(),
                    "server_software"=>$_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
                ]);
            } catch(Exception $e) {
                echo json_encode(["status"=>"ERROR", "message"=>$e->getMessage()]);
            }