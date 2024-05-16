<?php
require_once 'services/HabitService.class.php';

$habitService = new HabitService();

$user_id = 10; 

$title = $_POST['title'];
$description = $_POST['description'];
$unit = $_POST['unit'];
$verb = $_POST['verb'];
$increment = $_POST['increment'];
$milestone = $_POST['milestone'];

$result = $habitService->add_habit([
    'user_id' => $user_id,
    'title' => $title,
    'description' => $description,
    'unit' => $unit,
    'verb' => $verb,
    'increment' => $increment,
    'milestone' => $milestone
]);

if ($result) {
    echo json_encode(["message" => "Habit successfully created", "status" => "success"]);
} else {
    http_response_code(400);
    echo json_encode(["message" => "Failed to create habit", "status" => "error"]);
}
?>
