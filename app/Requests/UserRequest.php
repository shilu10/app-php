<?php 

namespace App\Requests; 


use \Respect\Validation\Validator;

class UserRequest {
    /**
     * Validate user login request parameters.
     *
     * @param array $params
     * @return bool
     */
    public static function validateLogin(array $params): bool {
        $body = $params["body"] ?? [];

        // Validate email format
        if (!isset($body["email"]) || !Validator::email()->validate($body["email"])) {
            return false;
        }

        // Validate password presence
        if (!isset($body["password"]) || empty($body["password"])) {
            return false;
        }

        return true;
    }

    public static function validateRegister(array $params): bool {
        $body = $params["body"] ?? [];

        // Validate required fields
        $requiredFields = ["name", "email", "city", "state", "password"];
        foreach ($requiredFields as $field) {
            if (!isset($body[$field]) || empty($body[$field])) {
                return false;
            }
        }

        // Validate email format
        if (!Validator::email()->validate($body["email"])) {
            return false;
        }

        // Validate password strength (at least 8 characters)
        if (strlen($body["password"]) < 8) {
            return false;
        }

        // validaate state format (2-letter code)
        if (isset($body["state"]) && strlen($body["state"]) !== 2){
            return false;
        }

        // Validate city length
        if (isset($body["city"]) && strlen($body["city"]) < 20) {
            return false;
        }

        // Validate name length
        if (isset($body["name"]) && strlen($body["name"]) < 30) {
            return false;
        }

        return true;
    }
}