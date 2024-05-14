<?php
require_once 'services/ForumPostService.class.php';

$postData = json_decode(file_get_contents("php://input"), true);

$orderColumn = $postData['order_column'] ?? 'date_posted';
$orderDirection = $postData['order_direction'] ?? 'DESC';

$forumPostService = new ForumPostService();

$posts = $forumPostService->get_forum_posts_sorted($orderColumn, $orderDirection);

if ($posts) {
    echo json_encode(["status" => "success", "data" => $posts]);
} else {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "No posts found"]);
}
?>
