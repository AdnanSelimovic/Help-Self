<?php

namespace HelpSelf;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Flight;

class Middleware
{
    public static function jwtAuth()
    {
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            list($jwt) = sscanf($authHeader, 'Bearer %s');

            if ($jwt) {
                try {
                    $decoded = JWT::decode($jwt, new Key(JWT_SECRET, 'HS256'));
                    Flight::set('user', (array)$decoded);
                    return true;
                } catch (\Exception $e) {
                    Flight::json(["error" => "true", "message" => "Invalid token"], 401);
                    return false;
                }
            }
        }

        Flight::json(["error" => "true", "message" => "Token not provided"], 401);
        return false;
    }
}
?>
