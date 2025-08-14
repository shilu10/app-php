<?php

namespace App\Controllers;

use \Framework\Database;
use \Config\DBConfig;

class HomeController{
    protected $db; 
    
    public function __construct() {
        $config = DBConfig::$settings;
        $db = new Database();
        $this->db = $db->getConnection($config);
    }

    public function home() {
        $stmt = $this->db->query("SELECT * FROM job_listings LIMIT 4");
        $data =[];

        $data["jobs"] = $stmt->fetchAll();
        loadView("home", $data);
    }

}
?>