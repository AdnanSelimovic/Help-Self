<?php
require_once 'services/UserService.class.php'; 

error_reporting(E_ALL);
ini_set('display_errors', 1);

$service = new UserService();
$data = json_decode(file_get_contents("php://input"), true);

$user_id = 10; 

$result = $service->update_user($user_id, $data);
if ($result) {
    echo json_encode(["status" => "success", "message" => "Profile updated successfully"]);
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Failed to update profile"]);
}
