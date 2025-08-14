<?php


/* 
* basePath takes path and returns the full absolute path 
* @params string path 
* @return string 
*/
function basePath($path) {
    $baseDir = __DIR__;
    
    return $baseDir . "/" . $path;
}

/*
* loadPartial takes partial name and outputs require file 
* @params string name 
* @return void
*/

function loadPartial($name) {
    $partialPath = "App/views/partials/";
    $fullPath = basePath($partialPath) . $name . ".php";

    require $fullPath; 
}

/*
* loadView takes view name and outputs require file 
* @params string name 
* @return void
*/

function loadView($name, $data=[]) {
    $ViewPath = "App/views/";
    $fullPath = basePath($ViewPath) . $name . ".php";

    extract($data);
    require $fullPath; 
}

/*
* inspect takes value and uses var_dump 
* @params mixed value 
* @return void 
*/

function inspect($value) {
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
}