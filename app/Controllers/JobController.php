<?php

namespace App\Controllers;

use \Framework\Database;
use \Config\DBConfig;
use \Framework\Session;
use Ramsey\Uuid\Uuid;
use PDOException;
use Exception;

class JobController {
    protected $db; 

    public function __construct() {
        $config = DBConfig::$settings; 
        $db = new Database();
        $db->connect($config);
        $this->db = $db;
    }

    /**
     * GET job details by ID
     */
    public function getDetails($params) {
        try {
            $id = $params['pathParams']["id"] ?? null;
            if (!$id) return ErrorController::badRequest("Missing job ID");

            $stmt = $this->db->bindQuery(
                "SELECT id, BIN_TO_UUID(user_id) as user_id, title, description, salary,
                        requirements, benefits, company, address, city, state, phone, email, tags
                 FROM job_listings
                 WHERE id = :id", 
                [':id' => $id]
            );

            if (!$stmt || $stmt->rowCount() === 0) {
                return ErrorController::notFound("Job not found");
            }

            $data["jobDetails"] = $stmt->fetch();
            loadView("details", $data);

        } catch (PDOException $e) {
            error_log("Database error in getDetails: " . $e->getMessage());
            return ErrorController::serverError();
        } catch (Exception $e) {
            error_log("General error in getDetails: " . $e->getMessage());
            return ErrorController::serverError();
        }
    }

    /**
     * GET /jobs/create
     */
    public function createGet() {
        try {
            loadView("posts");
        } catch (Exception $e) {
            error_log("Error in createGet: " . $e->getMessage());
            return ErrorController::serverError();
        }
    }

    /**
     * POST /jobs/create
     */
    public function createPost($params) {
        try {
            $body = $params['body'] ?? [];
            $body["user_id"] = Session::getUser()["id"] ?? null;

            $requiredFields = [
                'title','description','salary','requirements','benefits',
                'company','address','city','state','phone','email', 'tags', 'user_id'
            ];
            foreach ($requiredFields as $field) {
                if (!isset($body[$field])) $body[$field] = null;
            }

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
                return ErrorController::serverError("Job creation failed");
            }

        } catch (PDOException $e) {
            error_log("Database error in createPost: " . $e->getMessage());
            return ErrorController::serverError();
        } catch (Exception $e) {
            error_log("General error in createPost: " . $e->getMessage());
            return ErrorController::serverError();
        }
    }

    /**
     * DELETE /jobs/{id}
     */
    public function delete($params) {
        try {
            $id = $params['pathParams']["id"] ?? null;
            if (!$id) return ErrorController::badRequest("Missing job ID");

            $stmt = $this->db->bindQuery(
                "DELETE FROM job_listings WHERE id = :id", 
                [':id' => $id] 
            );

            if ($stmt && $stmt->rowCount() > 0) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Job deleted"
                ]);
            } else {
                return ErrorController::notFound("Job not found");
            }
            exit;

        } catch (PDOException $e) {
            error_log("Database error in delete: " . $e->getMessage());
            return ErrorController::serverError();
        } catch (Exception $e) {
            error_log("General error in delete: " . $e->getMessage());
            return ErrorController::serverError();
        }
    }

    /**
     * PATCH /jobs/{id}/edit
     */
    public function update($params) {
        try {
            $id = $params['pathParams']["id"] ?? null;
            if (!$id) return ErrorController::badRequest("Missing job ID");

            $rawBody = file_get_contents('php://input');
            $body = json_decode($rawBody, true) ?? [];
            $body['id'] = $id;
            $body["user_id"] = Session::getUser()["id"] ?? null;

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

            if ($stmt && $stmt->rowCount() > 0) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Job updated"
                ]);
            } else {
                return ErrorController::notFound("Job not found or no changes applied");
            }
            exit;

        } catch (PDOException $e) {
            error_log("Database error in update: " . $e->getMessage());
            return ErrorController::serverError();
        } catch (Exception $e) {
            error_log("General error in update: " . $e->getMessage());
            return ErrorController::serverError();
        }
    }

    /**
     * GET /jobs/{id}/edit
     */
    public function updateGet($params) {
        try {
            $id = $params['pathParams']["id"] ?? null;
            if (!$id) return ErrorController::badRequest("Missing job ID");

            $stmt = $this->db->bindQuery(
                "SELECT * FROM job_listings WHERE id = :id", 
                [':id' => $id] 
            );

            if (!$stmt || $stmt->rowCount() === 0) {
                return ErrorController::notFound("Job not found");
            }

            $data["job"] = $stmt->fetch();
            loadView("posts", $data);

        } catch (PDOException $e) {
            error_log("Database error in updateGet: " . $e->getMessage());
            return ErrorController::serverError();
        } catch (Exception $e) {
            error_log("General error in updateGet: " . $e->getMessage());
            return ErrorController::serverError();
        }
    }
}
