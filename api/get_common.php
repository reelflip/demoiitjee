<?php
include_once 'config.php';

try {
    $quotes = $conn->query("SELECT * FROM quotes ORDER BY RAND() LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    $flashcards = $conn->query("SELECT * FROM flashcards")->fetchAll(PDO::FETCH_ASSOC);
    $hacks = $conn->query("SELECT * FROM memory_hacks")->fetchAll(PDO::FETCH_ASSOC);
    $notifs = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    $posts = $conn->query("SELECT id, title, excerpt, content, author, category, image_url as imageUrl, created_at as date FROM blog_posts ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    
    $tests = $conn->query("SELECT * FROM tests")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($tests as &$test) {
        $qStmt = $conn->prepare("
            SELECT q.id, q.subject_id, q.topic_id, q.question_text, q.options_json, q.correct_option_index 
            FROM questions q 
            JOIN test_questions tq ON q.id = tq.question_id 
            WHERE tq.test_id = ? 
            ORDER BY tq.question_order ASC
        ");
        $qStmt->execute([$test['id']]);
        $questions = $qStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($questions as &$q) {
            $q['options'] = json_decode($q['options_json']);
            unset($q['options_json']);
        }
        $test['questions'] = $questions;
    }

    echo json_encode([ 
        "quotes" => $quotes, 
        "flashcards" => $flashcards, 
        "hacks" => $hacks, 
        "notifications" => $notifs, 
        "tests" => $tests,
        "blogPosts" => $posts
    ]);
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}