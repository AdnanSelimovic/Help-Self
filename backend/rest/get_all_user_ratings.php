<?php

// get_all_user_ratings.php
require_once 'services/RatingService.class.php';

$ratingService = new RatingService();
$user_id = 10;

$ratings = $ratingService->get_all_user_ratings($user_id);
echo json_encode(['status' => 'success', 'data' => $ratings]);
