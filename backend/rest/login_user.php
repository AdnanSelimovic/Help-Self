<?php
require_once 'services/UserService.class.php';

$userService = new UserService();

$email = $_POST['email'];
$password = $_POST['password'];

if ($userService->validateCredentials($email, $password)) {
    // Credentials are valid
    echo json_encode(["message" => "Login successful", "status" => "success"]);
} else {
    // Invalid credentials
    http_response_code(401);
    echo json_encode(["message" => "Invalid credentials", "status" => "error"]);
}
?>
