<?php
require_once 'services/ForumPostService.class.php';

$forumPostService = new ForumPostService();

$title = $_POST['title'] ?? null;
$content = $_POST['content'] ?? null;
$author_id = 10; 


if (empty($title) || empty($content)) {
    http_response_code(400);
    echo json_encode(["message" => "Title and content are required", "status" => "error"]);
    exit;
}


$post = [
    'author_id' => $author_id,
    'title' => $title,
    'content' => $content,
    'date_posted' => date("Y-m-d H:i:s") // current time
];


$result = $forumPostService->add_forum_post($post);

if ($result) {
    echo json_encode(["message" => "Post successfully created", "status" => "success"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Failed to create post", "status" => "error"]);
}
?>
