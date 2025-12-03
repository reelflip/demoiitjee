<?php
include_once 'config.php';

// Quotes
$quotes = $conn->query("SELECT * FROM quotes ORDER BY RAND() LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

// Flashcards
$flashcards = $conn->query("SELECT * FROM flashcards")->fetchAll(PDO::FETCH_ASSOC);

// Notifications
$notifs = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Tests
$tests = $conn->query("SELECT * FROM tests")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "quotes" => $quotes,
    "flashcards" => $flashcards,
    "notifications" => $notifs,
    "tests" => $tests
]);
?>