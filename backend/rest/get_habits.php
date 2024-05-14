<?php
require_once 'services/HabitService.class.php';


$userId = 10; 

$habitService = new HabitService();
$habits = $habitService->get_habits_by_user_id($userId);

if ($habits) {
    echo json_encode(["status" => "success", "data" => $habits]);
} else {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "No habits found for user"]);
}
?>
