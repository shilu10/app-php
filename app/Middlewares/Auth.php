<?php
namespace App\Middlewares;

use Framework\Session;

class Auth
{
    public function handle(array $params, callable $next)
    {
        if (!Session::getUser("user_email")) {
            header("Location: /users/login");
            exit;
        }

        // continue to next middleware or controller
        return $next($params);
    }
}
