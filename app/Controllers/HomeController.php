<?php

namespace App\Controllers;

use \Framework\Database;
use \Config\DBConfig;
use \App\ErrorController;

/*
    HomeController: maintains the home page.
*/
class HomeController{
    protected $db; 
    
    /*
    * constructor: initializes the database.    
    * @params: null 
    * @return: void
    */
    public function __construct() {
        $config = DBConfig::$settings;
        $db = new Database();
        $this->db = $db->getConnection($config);
    }

    /*
    * home: query job_listing table and loads the home view with it.    
    * @params: null 
    * @return: void || ErrorController
    */
    public function home() {
        try{
            $stmt = $this->db->query("SELECT * FROM job_listings LIMIT 4");

            if ($stmt === false) {
                ErrorController::serverError();
            }

            $jobs = $stmt->fetchAll();
            if (!$jobs){
                $jobs = [];
                return ErrorController::notFound();
            }
        }catch (PDOException $e){
            // Handles database-level errors
            error_log("Database error: " . $e->getMessage()); 
            return ErrorController::serverError();

        }catch (Exception $e) {
            // Handles generic errors
            error_log("General error: " . $e->getMessage());
            return ErrorController::serverError();
        }
                
        $data["jobs"] = $jobs;
        loadView("home", $data);
    }

}
?>