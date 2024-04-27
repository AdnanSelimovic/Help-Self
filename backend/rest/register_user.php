<?php
require_once 'services/UserService.class.php';

$userService = new UserService();

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password']; 

$result = $userService->add_user([
    'username' => $username,
    'email' => $email,
    // 'password' => password_hash($password, PASSWORD_DEFAULT) // Hashing the password
    'password' => $password
]);

if ($result) {
    echo json_encode(["message" => "User successfully registered", "status" => "success"]);
} else {
    http_response_code(400);
    echo json_encode(["message" => "Failed to register user", "status" => "error"]);
}
?>
