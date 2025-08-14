<?php

namespace App\Controllers;

use \Framework\Database;
use \Config\DBConfig;

class JobController {
    protected $db; 

    public function __construct(){
        $config = DBConfig::$settings; 
        $db = new Database();
        $db->connect($config);
        $this->db = $db;
    }

    public function getDetails($id) {
        $stmt = $this->db->bindQuery(
            "SELECT * FROM job_listings WHERE id = :id", 
            [':id' => $id] // Use dynamic ID from route
        );
        $data = [];
        $data["jobDetails"] = $stmt->fetch(); // Fetch all rows into an array

        loadView("details", $data);
    }

    public function createGet() {
        loadView("posts");
    }

    public function createPost($data) {
        $dummy_userid = rand(1, 1000);
        $data["user_id"] = $dummy_userid;
        $data["tags"] = "dummy";

        // Ensure all fields exist
        $requiredFields = [
            'title', 'description', 'salary', 'requirements', 'benefits',
            'company', 'address', 'city', 'state', 'phone', 'email'
        ];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                $data[$field] = null;
            }
        }

        $sql = "
            INSERT INTO job_listings (
                user_id,
                title,
                description,
                salary,
                requirements,
                benefits,
                company,
                address,
                city,
                state,
                phone,
                email,
                tags
            ) VALUES (
                :user_id,
                :title,
                :description,
                :salary,
                :requirements,
                :benefits,
                :company,
                :address,
                :city,
                :state,
                :phone,
                :email,
                :tags
            )
        ";

        $stmt = $this->db->bindQuery($sql, $data);

        if ($stmt && $stmt->rowCount() > 0) {
            header("Location: /");
            exit;
        } else {
            header("Location: /error");
            exit;
        }
    }

    public function delete($id){
        $stmt = $this->db->bindQuery(
            "DELETE FROM job_listings WHERE id = :id", 
            [':id' => $id] // Use dynamic ID from route
        );

        echo json_encode([
            "status" => "success",
            "message" => "Job deleted"
        ]);
        exit;
    }

    public function update($data) {
        var_dump($data);
        die();

        echo json_encode([
            "status" => "success",
            "message" => "Job deleted"
        ]);
        exit;
    }

    public function updateGet($id) {
        $stmt = $this->db->bindQuery(
            "SELECT * FROM job_listings WHERE id = :id", [':id' => $id] 
        );

        $data = [];
        $data["job"] = $stmt->fetch();

        loadView("posts", $data);
    }
}