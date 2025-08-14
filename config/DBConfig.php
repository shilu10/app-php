<?php
namespace Config;

class DBConfig {
    public static array $settings = [
        'host'     => 'localhost',
        'dbname'   => 'php',
        'username' => 'root',
        'password' => 'root',
        'charset'  => 'utf8',
        'port' => 3306, 
        'driver' => 'mysql',
    ];
}
