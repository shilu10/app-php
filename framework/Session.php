<?php

namespace Framework;

class Session{

    public static function startSession($user_name, $user_email) {

        session_set_cookie_params([
            'lifetime' => 3600,          // cookie lives 1 hour
            'path' => '/',               // cookie valid for entire domain
            'secure' => true,            // only send over HTTPS
            'httponly' => true,          // not accessible via JS
            'samesite' => 'Strict'       // Strict / Lax / None
        ]);

        session_start();
        $_SESSION["user_name"] = $user_name;
        $_SESSION["user_email"] = $user_email; 
        $_SESSION["logged"] = true;
    }

    public static function destroySession() {

        session_start();

        // Clear session array
        $_SESSION = [];

        // Destroy session file
        session_destroy();

        // Remove session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
    }
}