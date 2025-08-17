<?php 

namespace App\Controllers;

use \Framework\Database;
use \Config\DBConfig;
use \Framework\Session;
use \PDOException;
use \Exception;

class UserController {
    /**
     * @var Database
     */
    protected $db;

    /**
     * Constructor: Initializes the database connection.
     */
    public function __construct() {
        $config = DBConfig::$settings;
        $this->db = new Database();
        $this->db->getConnection($config); // Connection established (or exception thrown)
    }

    /**
     * POST /users/login
     * Authenticate a user and start a session.
     */
    public function login($params) {
        try {
            $body = $params["body"] ?? [];
            $email = $body["email"] ?? null;
            $password = $body["password"] ?? null;

            if (!$email || !$password) {
                return ErrorController::badRequest("Email and password are required");
            }

            $stmt = $this->db->query(
                "SELECT BIN_TO_UUID(id) as id, email, name, password, state, city 
                 FROM users 
                 WHERE email = :email",
                [":email" => $email]
            );

            if (!$stmt) {
                return ErrorController::serverError("Failed to query database");
            }

            $actualData = $stmt->fetch();

            if (!$actualData) {
                return ErrorController::notFound("User not found");
            }

            $verified = password_verify($password, $actualData["password"]);

            if ($verified) {
                Session::startSession($actualData["name"], $actualData["email"], $actualData["id"]);
                header("Location: /");
                exit;
            } else {
                return ErrorController::badRequest("Invalid email or password");
            }

        } catch (PDOException $e) {
            error_log("Database error in login: " . $e->getMessage());
            return ErrorController::serverError();
        } catch (Exception $e) {
            error_log("General error in login: " . $e->getMessage());
            return ErrorController::serverError();
        }
    }

    /**
     * POST /users/register
     * Register a new user.
     */
    public function register($params) {
        try {
            $body = $params["body"] ?? [];

            $required = ["name", "email", "city", "state", "password"];
            foreach ($required as $field) {
                if (empty($body[$field])) {
                    return ErrorController::badRequest("Missing required field: $field");
                }
            }

            $hashedPassword = password_hash($body["password"], PASSWORD_DEFAULT);

            $sql = "
                INSERT INTO users (
                    name, email, password, city, state
                ) VALUES (
                    :name, :email, :password, :city, :state
                )
            ";

            $values = [
                "email"    => $body["email"], 
                "password" => $hashedPassword, 
                "name"     => $body["name"], 
                "city"     => $body["city"],
                "state"    => $body["state"]
            ];

            $stmt = $this->db->bindQuery($sql, $values);

            if ($stmt && $stmt->rowCount() > 0) {
                header("Location: /users/login");
                exit;
            } else {
                return ErrorController::serverError("User registration failed");
            }

        } catch (PDOException $e) {
            error_log("Database error in register: " . $e->getMessage());
            return ErrorController::serverError();
        } catch (Exception $e) {
            error_log("General error in register: " . $e->getMessage());
            return ErrorController::serverError();
        }
    }

    /**
     * GET /users/login
     * Show login page.
     */
    public function loginGet() {
        loadView("login");
    }

    /**
     * GET /users/register
     * Show registration page.
     */
    public function registerGet() {
        loadView("register");
    }

    /**
     * GET /users/logout
     * Destroy session and log out.
     */
    public function logout() {
        Session::destroySession();
        header("Location: /");
        exit;
    }

    /**
     * GET /users/profile
     * Show the logged-in user's profile.
     */
    public function profile() {
        try {
            $user = Session::getUser();

            if (!$user) {
                return ErrorController::badRequest("No user logged in");
            }

            $sql = "SELECT name, email, city, state FROM users WHERE email = :email";
            $stmt = $this->db->bindQuery($sql, [":email" => $user["email"]]);

            if (!$stmt) {
                return ErrorController::serverError("Failed to fetch profile");
            }

            $userData = $stmt->fetch();

            if (!$userData) {
                return ErrorController::notFound("User profile not found");
            }

            $userData["logged"] = true;
            $data["currentUserDetails"] = $userData;

            loadView("profile", $data);

        } catch (PDOException $e) {
            error_log("Database error in profile: " . $e->getMessage());
            return ErrorController::serverError();
        } catch (Exception $e) {
            error_log("General error in profile: " . $e->getMessage());
            return ErrorController::serverError();
        }
    }
}
