<?php

namespace HelpSelf;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use OpenApi\Annotations as OA;
use Flight;

require_once 'vendor/autoload.php';
require_once __DIR__ . '/services/UserService.class.php';
require_once __DIR__ . '/services/HabitService.class.php';
require_once __DIR__ . '/services/ForumPostService.class.php';
require_once __DIR__ . '/services/RatingService.class.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/Middleware.php';

 /**
 * @OA\Info(
 * description="HelpSelf project API",
 * version="2.0.0",
 * title="Habit Tracking Application API",
 * @OA\Contact(
 * email="adnan.selimovic@stu.ibu.edu.ba"
 * )
 * )
 * @OA\Server(
 * description="API Mocking",
 * url="http://localhost/Help-Self/backend/rest"
 * )
 */
class Controller
{
    private $userService;
    private $habitService;
    private $forumPostService;
    private $ratingService;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->habitService = new HabitService();
        $this->forumPostService = new ForumPostService();
        $this->ratingService = new RatingService();
    }

    public function initRoutes()
    {
        // Public routes
        Flight::route('POST /backend/rest/register', [$this, 'register']);
        Flight::route('POST /backend/rest/login', [$this, 'login']);
        Flight::route('POST /backend/rest/send_password_reset', [$this, 'sendPasswordResetLink']);
        Flight::route('GET /backend/rest/verify_email', [$this, 'verifyEmail']);
        Flight::route('POST /backend/rest/resend_verification_email', [$this, 'resendVerificationEmail']);
        Flight::route('POST /backend/rest/get_recovery_codes', [$this, 'getRecoveryCodes']);
        Flight::route('POST /backend/rest/store_recovery_codes', [$this, 'storeRecoveryCodes']);

        Flight::route('/', function () {
            include 'frontend/index.html';
        });

        // Routes that require JWT Authentication
        Flight::route('POST /backend/rest/change_password', function () {
            Middleware::jwtAuth() && $this->changePassword();
        });
        Flight::route('POST /backend/rest/logout', function () {
            Middleware::jwtAuth() && $this->logout();
        });
        Flight::route('POST /backend/rest/create_forum_post', function () {
            Middleware::jwtAuth() && $this->createForumPost();
        });
        Flight::route('POST /backend/rest/create_habit', function () {
            Middleware::jwtAuth() && $this->createHabit();
        });
        Flight::route('DELETE /backend/rest/delete_habit', function () {
            Middleware::jwtAuth() && $this->deleteHabit();
        });
        Flight::route('GET /backend/rest/get_all_user_ratings', function () {
            Middleware::jwtAuth() && $this->getAllUserRatings();
        });
        Flight::route('POST /backend/rest/get_forum_posts_sorted', function () {
            Middleware::jwtAuth() && $this->getForumPostsSorted();
        });
        Flight::route('GET /backend/rest/get_habits', function () {
            Middleware::jwtAuth() && $this->getHabits();
        });
        Flight::route('GET /backend/rest/get_ratings', function () {
            Middleware::jwtAuth() && $this->getRatings();
        });
        Flight::route('GET /backend/rest/get_user_profile', function () {
            Middleware::jwtAuth() && $this->getUserProfile();
        });
        Flight::route('POST /backend/rest/rate_habit', function () {
            Middleware::jwtAuth() && $this->rateHabit();
        });
        Flight::route('POST /backend/rest/update_habit_details', function () {
            Middleware::jwtAuth() && $this->updateHabitDetails();
        });
        Flight::route('POST /backend/rest/update_habit_progress', function () {
            Middleware::jwtAuth() && $this->updateHabitProgress();
        });
        Flight::route('POST /backend/rest/update_user_profile', function () {
            Middleware::jwtAuth() && $this->updateUserProfile();
        });
    }


    /**
     * @OA\Post(
     *     path="/backend/rest/register",
     *     summary="Register a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="username", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="full_name", type="string"),
     *             @OA\Property(property="phone_number", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successfully registered",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="recovery_codes", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(response=500, description="Failed to register user")
     * )
     */
    public function register()
    {
        $request = Flight::request();
        $data = $request->data->getData();

        try {
            $result = $this->userService->add_user($data);
            if ($result) {
                Flight::json([
                    "error" => "false",
                    "message" => "User successfully registered",
                    "user_id" => $result['user']['id'],
                    "recovery_codes" => $result['recovery_codes'] // Return recovery codes
                ]);
            } else {
                Flight::json(["error" => "true", "message" => "Failed to register user"], 500);
            }
        } catch (\Exception $e) {
            Flight::halt(500, json_encode(["error" => "true", "message" => $e->getMessage()]));
        }
    }


    /**
     * @OA\Post(
     *     path="/backend/rest/login",
     *     summary="Login a user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="username", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successfully logged in",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Invalid credentials"),
     *     @OA\Response(response=500, description="Failed to login user")
     * )
     */
    public function login()
    {
        $request = Flight::request();
        $data = $request->data->getData();

        try {
            $isValid = $this->userService->validateCredentials($data['username'], $data['password']);
            if ($isValid) {
                $user = $this->userService->get_user_by_login($data['username']);
                $payload = [
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email']
                    ],
                    'iat' => time(),
                    'exp' => time() + (60 * 60) // 1 hour expiration
                ];

                $token = JWT::encode($payload, JWT_SECRET, 'HS256');
                Flight::json([
                    "error" => "false",
                    "message" => "User successfully logged in",
                    "token" => $token
                ]);
            } else {
                Flight::json(["error" => "true", "message" => "Invalid credentials"], 401);
            }
        } catch (\Exception $e) {
            Flight::halt(500, json_encode(["error" => "true", "message" => $e->getMessage()]));
        }
    }

    /**
     * @OA\Post(
     *     path="/backend/rest/logout",
     *     summary="Logout a user",
     *     @OA\Response(
     *         response=200,
     *         description="User successfully logged out",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function logout()
    {
        Flight::json(["error" => "false", "message" => "User successfully logged out"]);
    }

    /**
     * @OA\Post(
     *     path="/backend/rest/send_password_reset",
     *     summary="Send password reset link",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Temporary password sent to your email",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Failed to send password reset link")
     * )
     */
    public function sendPasswordResetLink()
    {
        $request = Flight::request();
        $data = $request->data->getData();

        try {
            $this->userService->sendPasswordResetLink($data['email']);
            Flight::json(["error" => "false", "message" => "Temporary password sent to your email"]);
        } catch (\Exception $e) {
            Flight::halt(400, json_encode(["error" => "true", "message" => $e->getMessage()]));
        }
    }

    /**
     * @OA\Get(
     *     path="/backend/rest/verify_email",
     *     summary="Verify email",
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email successfully verified",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid or expired token",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to verify email",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function verifyEmail()
    {
        error_log("verifyEmail route called");
        $token = Flight::request()->query['token'];

        try {
            $isVerified = $this->userService->verifyEmail($token);
            if ($isVerified) {
                Flight::json(["error" => "false", "message" => "Email successfully verified"]);
            } else {
                Flight::json(["error" => "true", "message" => "Invalid or expired token"], 400);
            }
        } catch (\Exception $e) {
            Flight::halt(500, json_encode(["error" => "true", "message" => $e->getMessage()]));
        }
    }

    /**
     * @OA\Post(
     *     path="/backend/rest/resend_verification_email",
     *     summary="Resend verification email",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Verification email sent",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to resend verification email",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function resendVerificationEmail()
    {
        $request = Flight::request();
        $data = $request->data->getData();

        try {
            $this->userService->resendVerificationEmail($data['email']);
            Flight::json(["error" => "false", "message" => "Verification email sent"]);
        } catch (\Exception $e) {
            Flight::halt(400, json_encode(["error" => "true", "message" => $e->getMessage()]));
        }
    }

    /**
     * @OA\Post(
     *     path="/backend/rest/change_password",
     *     summary="Change user password",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="new_password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password successfully changed",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="User ID and new password are required",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to change password",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function changePassword()
    {
        $request = Flight::request();
        $data = $request->data->getData();

        error_log("Received data: " . json_encode($data));

        if (empty($data['user_id']) || empty($data['new_password'])) {
            Flight::halt(400, json_encode(["error" => "true", "message" => "User ID and new password are required."]));
            return;
        }

        try {
            $this->userService->changePassword($data['user_id'], $data['new_password']);
            Flight::json(["error" => "false", "message" => "Password successfully changed."]);
        } catch (\Exception $e) {
            Flight::halt(500, json_encode(["error" => "true", "message" => $e->getMessage()]));
        }
    }

    /**
     * @OA\Post(
     *     path="/backend/rest/get_recovery_codes",
     *     summary="Get recovery codes for a user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Recovery codes retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="recovery_codes", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="User ID is required",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to retrieve recovery codes",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function getRecoveryCodes()
    {
        $request = Flight::request();
        $data = $request->data->getData();

        error_log("Received data: " . json_encode($data));

        if (empty($data['user_id'])) {
            Flight::halt(400, json_encode(["error" => "true", "message" => "User ID is required."]));
            return;
        }

        try {
            $recoveryCodes = $this->userService->getRecoveryCodes($data['user_id']);
            Flight::json(["recovery_codes" => $recoveryCodes]);
        } catch (\Exception $e) {
            Flight::halt(500, json_encode(["error" => "true", "message" => $e->getMessage()]));
        }
    }


    /**
     * @OA\Post(
     *     path="/backend/rest/store_recovery_codes",
     *     summary="Store recovery codes for a user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="recovery_codes", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Recovery codes stored successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="User ID and recovery codes are required",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to store recovery codes",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function storeRecoveryCodes()
    {
        $request = Flight::request();
        $data = $request->data->getData();

        if (empty($data['user_id']) || empty($data['recovery_codes'])) {
            Flight::halt(400, json_encode(["error" => "true", "message" => "User ID and recovery codes are required."]));
            return;
        }

        try {
            $this->userService->store_recovery_codes($data['user_id'], $data['recovery_codes']);
            Flight::json(["error" => "false", "message" => "Recovery codes stored successfully"]);
        } catch (\Exception $e) {
            Flight::halt(500, json_encode(["error" => "true", "message" => $e->getMessage()]));
        }
    }

    /**
     * @OA\Post(
     *     path="/backend/rest/create_forum_post",
     *     summary="Create a new forum post",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="content", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post successfully created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="User ID is required",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to create post",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     )
     * )
     */
    public function createForumPost()
    {
        $data = Flight::request()->data->getData();
        if (!isset($data['user_id'])) {
            Flight::halt(400, json_encode(["error" => "true", "message" => "User ID is required."]));
            return;
        }
        $post = [
            'author_id' => $data['user_id'],
            'title' => $data['title'],
            'content' => $data['content'],
            'date_posted' => date("Y-m-d H:i:s")
        ];
        $result = $this->forumPostService->add_forum_post($post);
        if ($result) {
            Flight::json(["message" => "Post successfully created", "status" => "success"]);
        } else {
            Flight::json(["message" => "Failed to create post", "status" => "error"], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/backend/rest/create_habit",
     *     summary="Create a new habit",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="unit", type="string"),
     *             @OA\Property(property="verb", type="string"),
     *             @OA\Property(property="increment", type="integer"),
     *             @OA\Property(property="milestone", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Habit successfully created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="User ID is required",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to create habit",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     )
     * )
     */
    public function createHabit()
    {
        $data = Flight::request()->data->getData();
        if (empty($data['user_id'])) {
            Flight::halt(400, json_encode(["error" => "true", "message" => "User ID is required."]));
            return;
        }

        $result = $this->habitService->add_habit([
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'unit' => $data['unit'],
            'verb' => $data['verb'],
            'increment' => $data['increment'],
            'milestone' => $data['milestone']
        ]);

        if ($result) {
            Flight::json(["message" => "Habit successfully created", "status" => "success"]);
        } else {
            Flight::json(["message" => "Failed to create habit", "status" => "error"], 400);
        }
    }


    /**
     * @OA\Delete(
     *     path="/backend/rest/delete_habit",
     *     summary="Delete a habit",
     *     @OA\Parameter(
     *         name="habitId",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Habit successfully deleted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Habit ID is required",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to delete habit",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     )
     * )
     */
    public function deleteHabit()
    {
        $habitId = Flight::request()->query['habitId'] ?? null;
        if (empty($habitId)) {
            Flight::json(["message" => "Habit ID is required", "status" => "error"], 400);
            return;
        }
        try {
            if ($this->habitService->delete_habit_by_id($habitId)) {
                Flight::json(["message" => "Habit successfully deleted", "status" => "success"]);
            } else {
                throw new \Exception("Deletion failed at the database level");
            }
        } catch (\Exception $e) {
            Flight::json(["message" => $e->getMessage(), "status" => "error"], 400);
        }
    }


    /**
     * @OA\Get(
     *     path="/backend/rest/get_all_user_ratings",
     *     summary="Get all ratings for a user",
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User ratings retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="average", type="number", format="float")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="User ID is required",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to retrieve user ratings",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function getAllUserRatings()
    {
        $userId = Flight::request()->query['user_id'] ?? null;
        if ($userId === null) {
            Flight::halt(400, json_encode(["error" => "true", "message" => "User ID is required"]));
            return;
        }

        $ratings = $this->ratingService->get_all_user_ratings($userId);
        $averageRating = $this->ratingService->get_average_rating_for_user($userId);
        Flight::json([
            'status' => 'success',
            'data' => $ratings,
            'average' => $averageRating['average_rating']
        ]);
    }


    /**
     * @OA\Post(
     *     path="/backend/rest/get_forum_posts_sorted",
     *     summary="Get forum posts sorted by specified criteria",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="order_column", type="string", example="date_posted"),
     *             @OA\Property(property="order_direction", type="string", example="DESC")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Forum posts retrieved and sorted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to retrieve forum posts",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function getForumPostsSorted()
    {
        $data = Flight::request()->data->getData();
        $order_column = $data['order_column'] ?? 'date_posted';
        $order_direction = $data['order_direction'] ?? 'DESC';

        try {
            $posts = $this->forumPostService->get_forum_posts_sorted($order_column, $order_direction);
            Flight::json(["status" => "success", "data" => $posts]);
        } catch (\Exception $e) {
            Flight::halt(500, json_encode(["error" => "true", "message" => $e->getMessage()]));
        }
    }


    /**
     * @OA\Get(
     *     path="/backend/rest/get_habits",
     *     summary="Get all habits for a user",
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Habits retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="User ID is required",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No habits found for user",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function getHabits()
    {
        $userId = Flight::request()->query['user_id'] ?? null;
        if ($userId === null) {
            Flight::halt(400, json_encode(["error" => "true", "message" => "User ID is required"]));
            return;
        }

        $habits = $this->habitService->get_habits_by_user_id($userId);
        if ($habits) {
            Flight::json(["status" => "success", "data" => $habits]);
        } else {
            Flight::json(["status" => "error", "message" => "No habits found for user"]);
        }
    }

    /**
     * @OA\Get(
     *     path="/backend/rest/get_ratings",
     *     summary="Get all ratings for a user",
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ratings retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="User ID is required",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No ratings found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function getRatings()
    {
        $userId = Flight::request()->query['user_id'] ?? null;
        if ($userId === null) {
            Flight::halt(400, json_encode(["error" => "true", "message" => "User ID is required"]));
            return;
        }

        $ratings = $this->ratingService->get_ratings_by_user_id($userId);
        if ($ratings) {
            Flight::json(['status' => 'success', 'data' => $ratings]);
        } else {
            Flight::json(['status' => 'error', 'message' => 'No ratings found']);
        }
    }


    /**
     * @OA\Get(
     *     path="/backend/rest/get_user_profile",
     *     summary="Get user profile by user ID",
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User profile retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="User ID is required",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function getUserProfile()
    {
        $userId = Flight::request()->query['user_id'] ?? null;
        if ($userId === null) {
            Flight::halt(400, json_encode(["error" => "true", "message" => "User ID is required"]));
            return;
        }

        $result = $this->userService->get_user_by_id($userId);
        if ($result) {
            Flight::json(["status" => "success", "data" => $result]);
        } else {
            Flight::json(["status" => "error", "message" => "User not found"], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/backend/rest/rate_habit",
     *     summary="Rate a habit",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="habitId", type="integer"),
     *             @OA\Property(property="rating", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rating successfully added",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to add rating",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     )
     * )
     */
    public function rateHabit()
    {
        $data = Flight::request()->data->getData();
        $rating = [
            "habit_id" => $data['habitId'],
            "value" => $data['rating'],
            "date" => date("Y-m-d")
        ];
        $result = $this->ratingService->add_rating($rating);
        if ($result) {
            Flight::json(["message" => "Rating successfully added", "status" => "success"]);
        } else {
            Flight::json(["message" => "Failed to add rating", "status" => "error"], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/backend/rest/update_habit_details",
     *     summary="Update habit details",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="habitId", type="integer"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="milestone", type="integer"),
     *             @OA\Property(property="increment", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Habit details updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="All fields are required",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to update habit details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     )
     * )
     */
    public function updateHabitDetails()
    {
        $data = Flight::request()->data->getData();
        $habitId = $data['habitId'] ?? null;
        $description = $data['description'] ?? null;
        $milestone = $data['milestone'] ?? null;
        $increment = $data['increment'] ?? null;
        if (empty($habitId) || empty($description) || empty($milestone) || empty($increment)) {
            Flight::json(["message" => "All fields are required", "status" => "error"], 400);
            return;
        }
        $result = $this->habitService->update_habit_details($habitId, $description, $milestone, $increment);
        if ($result) {
            Flight::json(["message" => "Habit details updated successfully", "status" => "success"]);
        } else {
            Flight::json(["message" => "Failed to update habit details", "status" => "error"], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/backend/rest/update_habit_progress",
     *     summary="Update habit progress",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="habitId", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Habit updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Habit ID is required or Failed to update habit progress",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     )
     * )
     */
    public function updateHabitProgress()
    {
        $data = Flight::request()->data->getData();
        $habitId = $data['habitId'] ?? null;
        try {
            if (!$habitId)
                throw new \Exception("Habit ID is required");
            $result = $this->habitService->increment_habit_progress($habitId);
            if ($result) {
                Flight::json(["message" => "Habit updated successfully!", "status" => "success"]);
            } else {
                throw new \Exception("Failed to update habit progress");
            }
        } catch (\Exception $e) {
            Flight::json(["message" => $e->getMessage(), "status" => "error"], 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/backend/rest/update_user_profile",
     *     summary="Update user profile",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="biography", type="string"),
     *             @OA\Property(property="location", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="User ID is required or Failed to update profile",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function updateUserProfile()
    {
        $request = Flight::request();
        $data = $request->data->getData();

        // Log the received data
        error_log("Received data: " . json_encode($data));

        // Check if user_id is set in the data
        if (!isset($data['user_id'])) {
            Flight::halt(400, json_encode(["error" => "true", "message" => "User ID is required."]));
            return;
        }

        // Proceed with the update
        $result = $this->userService->update_user($data['user_id'], $data);
        if ($result) {
            Flight::json(["status" => "success", "message" => "Profile updated successfully"]);
        } else {
            Flight::json(["status" => "error", "message" => "Failed to update profile"], 400);
        }
    }

}
?>