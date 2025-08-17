<?php 

namespace App\Controllers;

use \Framework\Database;
use \Config\DBConfig;
use \Framework\Session;
use Ramsey\Uuid\Uuid;

class UserController {

    public function __construct() {
        $config = DBConfig::$settings;
        $this->db = new Database();
        $this->db->getConnection($config); // Initialize connection, but keep $this->db as Database
    }

    public function login($params) {
        $body = $params["body"];

        $email = $body["email"];
        $password = $body["password"];
        
        $stmt = $this->db->query("SELECT BIN_TO_UUID(id) as id, email, name, password, state, city FROM users WHERE email=:email", ["email"=>$email]);
        $actualData = $stmt->fetch();
        $verified = password_verify($password, $actualData["password"]);

        var_dump($actualData);

        if ($verified) {
            Session::startSession($actualData["name"], $actualData["email"], $actualData["id"]);
            header("Location: /");
            exit;
        } else {
            header("Location: /error");
            exit;
        }
        
    }

    public function register ($params) {
        $body = $params["body"];

        $name = $body["name"];
        $email = $body["email"];
        $city = $body["city"];
        $state = $body["state"];
        $password = $body["password"];

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "
            INSERT INTO users (
                name, email, password, city, state
            ) VALUES (
                :name, :email, :password, :city, :state
            )
        ";

        $values = [
                   "email"=>$email, 
                   "password"=>$hashedPassword, 
                   "name"=>$name, 
                   "city"=>$city,
                   "state"=>$state];

        $stmt = $this->db->bindQuery($sql, $values);

        if ($stmt && $stmt->rowCount() > 0) {
            header("Location: /users/login");
            exit;
        } else {
            header("Location: /error");
            exit;
        }

    }

    public function loginGet() {
        loadView("login");
    }

    public function registerGet() {
        loadView("register");
    }

    public function logout() {
        Session::destroySession();
        header("Location: /");
    }

    public function profile() {
        $user = Session::getUser();
        $sql = "SELECT name, email, city, state FROM users WHERE email=:email";
        $stmt = $this->db->bindQuery($sql, ["email" => $user["email"]]);

        $userData = $stmt->fetch();
        $userData["logged"] = true;
        $currentUser["currentUserDetails"] = $userData;

        loadView("profile", $currentUser);
    }

}