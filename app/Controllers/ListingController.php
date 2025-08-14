<?php

namespace App\Controllers;

use \Framework\Database;
use \Config\DBConfig;

class ListingController{
    protected $db; 
    
    public function __construct() {
        $config = DBConfig::$settings;
        $this->db = new Database();
        $this->db->getConnection($config); // Initialize connection, but keep $this->db as Database
    }

    public function listAll(){
        $stmt = $this->db->query("SELECT * FROM job_listings");
        $data =[];

        $data["jobs"] = $stmt->fetchAll();
        loadView("listings", $data);
    }

}

?>