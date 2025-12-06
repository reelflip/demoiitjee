<?php require 'config.php'; header('Content-Type: application/json'); 
        $tf=$conn->query("SELECT * FROM site_traffic ORDER BY visit_date DESC LIMIT 7")->fetchAll(PDO::FETCH_ASSOC);
        $total=$conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $visits=array_sum(array_column($tf,'visit_count'));
        $daily=array_reverse(array_map(function($r){ return ['date'=>date('D',strtotime($r['visit_date'])), 'visits'=>(int)$r['visit_count']]; }, $tf));
        $growth=$conn->query("SELECT DATE(created_at) as d, COUNT(*) as c FROM users GROUP BY DATE(created_at) ORDER BY d DESC LIMIT 7")->fetchAll(PDO::FETCH_ASSOC);
        $growthData=array_reverse(array_map(function($r){ return ['date'=>date('M d',strtotime($r['d'])), 'users'=>(int)$r['c']]; }, $growth));
        echo json_encode(['totalVisits'=>$visits,'totalUsers'=>$total,'dailyTraffic'=>$daily,'userGrowth'=>$growthData]);