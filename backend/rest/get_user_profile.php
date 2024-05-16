<?php
require_once 'services/UserService.class.php'; 

$service = new UserService();
$user_id = 10; 

$result = $service->get_user_by_id($user_id);
if ($result) {
    echo json_encode(["status" => "success", "data" => $result]);
} else {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "User not found"]);
}
