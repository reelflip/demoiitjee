<?php
include_once 'config.php';
$data = json_decode(file_get_contents("php://input"));
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Create Blog Post
    try {
        $stmt = $conn->prepare("INSERT INTO blog_posts (id, title, excerpt, content, author, category, image_url, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data->id,
            $data->title,
            $data->excerpt,
            $data->content,
            $data->author,
            $data->category,
            $data->imageUrl,
            $data->date
        ]);
        echo json_encode(["message" => "Blog post created"]);
    } catch (Exception $e) { http_response_code(500); echo json_encode(["error" => $e->getMessage()]); }
} elseif ($method === 'DELETE' && isset($_GET['id'])) {
    // Delete Blog Post
    try {
        $stmt = $conn->prepare("DELETE FROM blog_posts WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        echo json_encode(["message" => "Blog post deleted"]);
    } catch (Exception $e) { http_response_code(500); echo json_encode(["error" => $e->getMessage()]); }
}
?>