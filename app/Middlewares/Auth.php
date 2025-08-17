<?php

namespace App\Middlewares;

class Auth{
    public function __construct() {
        // Initialize any required properties or dependencies
    }

    public function handle($params) {
        session_start();
        // Check if user is logged in
        if (!isset($_SESSION["user_email"])) {
            // Redirect to login page if not authenticated
            header("Location: /users/login");
            exit;
        }

        // If authenticated, allow the request to proceed
        return true;
    }
}

?>

