<?php
require_once 'services/HabitService.class.php';

$habitService = new HabitService();
$habitId = $_POST['habitId'] ?? null;

try {
    if (!$habitId) throw new Exception("Habit ID is required");

    $result = $habitService->increment_habit_progress($habitId);
    if ($result) {
        echo json_encode(["message" => "Habit updated successfully!", "status" => "success"]);
    } else {
        throw new Exception("Failed to update habit progress");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["message" => $e->getMessage(), "status" => "error"]);
}
?>
