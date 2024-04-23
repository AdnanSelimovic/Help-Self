<?php
require_once 'services/RatingService.class.php';

$ratingService = new RatingService();


$user_id = 10;

$ratings = $ratingService->get_ratings_by_user_id($user_id);
if ($ratings) {
    echo json_encode(['status' => 'success', 'data' => $ratings]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No ratings found']);
}
?>