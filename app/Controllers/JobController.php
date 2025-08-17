<?php

namespace App\Controllers;

use \Framework\Database;
use \Config\DBConfig;
use \Framework\Session;
use Ramsey\Uuid\Uuid;

class JobController {
    protected $db; 

    public function __construct() {
        // Load DB configuration and connect
        $config = DBConfig::$settings; 
        $db = new Database();
        $db->connect($config);
        $this->db = $db;
    }

    /**
     * GET job details by ID (path parameter)
     */
    public function getDetails($params) {
        var_dump($params);
        $pathParams  = $params['pathParams']["id"];
        $queryParams = $params['queryParams'];

        $stmt = $this->db->bindQuery(
            "SELECT id, BIN_TO_UUID(user_id) as user_id, title, description, salary,
                requirements, benefits, company, address, city, state, phone, email, tags
            FROM job_listings
            WHERE id = :id", 
            [':id' => $pathParams] // Bind dynamic ID
        );

        $data = [];
        $data["jobDetails"] = $stmt->fetch(); // Fetch single row

        loadView("details", $data);
    }

    /**
     * GET /jobs/create
     */
    public function createGet() {
        loadView("posts");
    }

    /**
     * POST /jobs/create
     * Create a new job
     */
    public function createPost($params) {
        // Parse request body (form or JSON)
        $body = $params['body'];
        $body["user_id"] = Session::getUser()["id"];
        
        // Ensure all required fields exist
        $requiredFields = [
            'title','description','salary','requirements','benefits',
            'company','address','city','state','phone','email', 'tags', 'user_id'
        ];
        foreach ($requiredFields as $field) {
            if (!isset($body[$field])) $body[$field] = null;
        }

        echo "inside";

        $sql = "
            INSERT INTO job_listings (
                user_id, title, description, salary, requirements, benefits,
                company, address, city, state, phone, email, tags
            ) VALUES (
                UUID_TO_BIN(:user_id), :title, :description, :salary, :requirements, :benefits,
                :company, :address, :city, :state, :phone, :email, :tags
            )
        ";

        $stmt = $this->db->bindQuery($sql, $body);

        if ($stmt && $stmt->rowCount() > 0) {
            header("Location: /");
            exit;
        } else {
            header("Location: /error");
            exit;
        }
    }

    /**
     * DELETE /jobs/{id}
     */
    public function delete($params){
        $pathParams = $params['pathParams']["id"];

        $stmt = $this->db->bindQuery(
            "DELETE FROM job_listings WHERE id = :id", 
            [':id' => $pathParams] 
        );

        echo json_encode([
            "status" => "success",
            "message" => "Job deleted"
        ]);
        exit;
    }

    /**
     * PATCH /jobs/{id}/edit
     * Update an existing job
     */
    public function update($params) {
        $pathParams = $params['pathParams']["id"];

        // Read raw JSON body for PATCH
        $rawBody = file_get_contents('php://input');
        $body = json_decode($rawBody, true); // Decode JSON into associative array

        if (!$body) $body = []; // Handle empty body

        // Add default fields if missing
        if (!isset($body['tags'])) $body['tags'] = '';
        $body['id'] = $pathParams; // Required for WHERE clause

        // getting user_id from current session 
        $body["user_id"] = Session::getUser()["id"];

        // Ensure all placeholders exist to avoid SQL errors
        $requiredFields = [
            'title','description','salary','requirements','benefits',
            'company','address','city','state','phone','email', 'tags', 'user_id'
        ];
        foreach ($requiredFields as $field) {
            if (!isset($body[$field])) $body[$field] = null;
        }

        $sql = "
            UPDATE job_listings
            SET 
                user_id = UUID_TO_BIN(:user_id),
                title = :title,
                description = :description,
                salary = :salary,
                requirements = :requirements,
                benefits = :benefits,
                company = :company,
                address = :address,
                city = :city,
                state = :state,
                phone = :phone,
                email = :email,
                tags = :tags
            WHERE id = :id
        ";

        $stmt = $this->db->bindQuery($sql, $body);

        echo json_encode([
            "status" => "success",
            "message" => "Job updated"
        ]);
        exit;
    }

    /**
     * GET /jobs/{id}/edit
     * Load job for editing
     */
    public function updateGet($params) {
        $pathParams = $params['pathParams']["id"];

        $stmt = $this->db->bindQuery(
            "SELECT * FROM job_listings WHERE id = :id", 
            [':id' => $pathParams] 
        );

        $data = [];
        $data["job"] = $stmt->fetch();

        loadView("posts", $data);
    }
}
