<?php

namespace App\Controllers;

use \Framework\Database;
use \Config\DBConfig;
use \PDOException;
use \Exception;

class ListingController {
    /**
     * @var Database The database wrapper instance
     */
    protected $db; 
    
    /**
     * Constructor: Initializes the database connection.
     * 
     * Note:
     * - If the DB connection fails, the exception will bubble up
     *   and should be caught by the global exception handler.
     */
    public function __construct() {
        $config = DBConfig::$settings;
        $this->db = new Database();
        $this->db->getConnection($config); // Establish lazy-loaded connection
    }

    /**
     * GET /listings
     * Fetch all job listings and load them into a view.
     * 
     * Error Handling:
     * - If a DB error occurs, it logs the error and shows a server error response.
     */
    public function listAll() {
        try {
            // Query all job listings
            $stmt = $this->db->query("SELECT * FROM job_listings");

            if (!$stmt) {
                // If query failed, show a server error
                return ErrorController::serverError("Failed to fetch job listings");
            }

            $data = [];
            $data["jobs"] = $stmt->fetchAll();

            loadView("listings", $data);

        } catch (PDOException $e) {
            // Handles SQL/DB errors
            error_log("Database error in listAll: " . $e->getMessage());
            return ErrorController::serverError();

        } catch (Exception $e) {
            // Handles unexpected errors
            error_log("General error in listAll: " . $e->getMessage());
            return ErrorController::serverError();
        }
    }
}
