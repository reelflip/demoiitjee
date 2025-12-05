<?php require 'config.php'; header('Content-Type: application/json'); $d=json_decode(file_get_contents("php://input")); 
        if(isset($d->action) && $d->action == 'search') {
            $q = $d->query;
            $stmt = $conn->prepare("SELECT id, full_name as name, email FROM users WHERE role='STUDENT' AND (email LIKE ? OR full_name LIKE ? OR id LIKE ?) LIMIT 5");
            $stmt->execute(["%$q%", "%$q%", "%$q%"]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            exit();
        }
        $sid=$d->student_identifier; 
        if(strpos($sid,'@')){ $u=$conn->query("SELECT id FROM users WHERE email='$sid'")->fetch(); $sid=$u?$u['id']:null; } 
        if($sid){ 
            $req=json_encode(['fromId'=>$d->parent_id, 'fromName'=>$d->parent_name]); 
            $conn->prepare("UPDATE users SET pending_request_json=? WHERE id=?")->execute([$req, $sid]); 
            echo json_encode(["message"=>"Success"]); 
        }else{ echo json_encode(["message"=>"User not found"]); }