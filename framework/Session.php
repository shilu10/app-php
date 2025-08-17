<?php

namespace Framework;

class Session{

    /**
     * Start a session and store user details securely.
     *
     * @param string $user_name
     * @param string $user_email
     */
    public static function startSession($user_name, $user_email) {

        // Configure session cookie security options
        session_set_cookie_params([
            'lifetime' => 3600,          // Cookie expires in 1 hour
            'path' => '/',               // Cookie is valid for the whole domain
            'secure' => true,            // Only send cookie over HTTPS
            'httponly' => true,          // Prevent access via JavaScript (XSS protection)
            'samesite' => 'Strict'       // Prevent CSRF attacks (can also be 'Lax' or 'None')
        ]);

        // Start the session
        session_start();

        // Store user data in the session
        $_SESSION["user_name"]  = $user_name;
        $_SESSION["user_email"] = $user_email;
        $_SESSION["logged"]     = true;
    }

    /**
     * Destroy the current session and remove all data.
     */
    public static function destroySession() {

        // Ensure session is started before destroying
        session_start();

        // Clear all session variables
        $_SESSION = [];

        // Destroy the session file on server
        session_destroy();

        // Remove the session cookie from the browser
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),  // Name of the session cookie
                '',              // Empty value
                time() - 42000,  // Expire in the past
                $params["path"], 
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
    }

    /*
    * gets current user
    * returns associate array user details
    */
    public static function getUser() {
        session_start();
        return $_SESSION["logged"] ? [
            "name"  => $_SESSION["user_name"],
            "email" => $_SESSION["user_email"]
        ] : null;
    }

}
