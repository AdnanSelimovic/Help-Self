<?php
require_once 'services/HabitService.class.php';

// Statically setting the user ID for now
$userId = 10; // You can change this as needed for testing different users

$habitService = new HabitService();
$habits = $habitService->get_habits_by_user_id($userId);

if ($habits) {
    echo json_encode(["status" => "success", "data" => $habits]);
} else {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "No habits found for user"]);
}
?>
