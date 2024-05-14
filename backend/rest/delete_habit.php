<?php
require_once 'services/HabitService.class.php';

$habitService = new HabitService();
$inputData = file_get_contents("php://input");
parse_str($inputData, $deleteData);

$habitId = $deleteData['habitId'] ?? null;

if (empty($habitId)) {
    http_response_code(400);
    echo json_encode(["message" => "Habit ID is required", "status" => "error"]);
    exit;
}

try {
    if ($habitService->delete_habit_by_id($habitId)) {
        echo json_encode(["message" => "Habit successfully deleted", "status" => "success"]);
    } else {
        throw new Exception("Deletion failed at the database level");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["message" => $e->getMessage(), "status" => "error"]);
}

?>
