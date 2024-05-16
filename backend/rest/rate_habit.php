<?php
require_once 'services/RatingService.class.php';


$userId = 10;  
$habitId = $_POST['habitId'];
$ratingValue = $_POST['rating'];

$ratingService = new RatingService();

// Validate input
if (!isset($habitId, $ratingValue)) {
    http_response_code(400);
    echo json_encode(["message" => "Missing habit ID or rating", "status" => "error"]);
    exit;
}

// Prepare rating data
$rating = [
    "habit_id" => $habitId,
    "value" => $ratingValue,
    "date" => date("Y-m-d")  // Assuming the rating date is today
];

// Attempt to add the rating
$result = $ratingService->add_rating($rating);

if ($result) {
    echo json_encode(["message" => "Rating successfully added", "status" => "success"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Failed to add rating", "status" => "error"]);
}
?>
