<?php
require_once 'services/HabitService.class.php';

$habitService = new HabitService();


$habitId = $_POST['habitId'] ?? null;
$description = $_POST['description'] ?? null;
$milestone = $_POST['milestone'] ?? null;  
$increment = $_POST['increment'] ?? null;


if (empty($habitId) || empty($description) || empty($milestone) || empty($increment)) {
    http_response_code(400);
    echo json_encode(["message" => "All fields are required", "status" => "error"]);
    exit;
}


$result = $habitService->update_habit_details($habitId, $description, $milestone, $increment);

if ($result) {
    echo json_encode(["message" => "Habit details updated successfully", "status" => "success"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Failed to update habit details", "status" => "error"]);
}
?>