<?php 

function registerListingRoutes($router) {
    $router->GET("/listings", "ListingController@listAll");
}

function registerHomeRoutes($router) {
    $router->GET("/", "HomeController@home");
}

function registerJobRoutes($router) {
    $router->GET("jobs/details/{id}", "JobController@getDetails");
    $router->DELETE("jobs/{id}", "JobController@delete");
    $router->POST("jobs/", "JobController@createPost");
    $router->GET("jobs/", "JobController@createGet");
    $router->PATCH("jobs/{id}/edit", "JobController@update");
    $router->GET("jobs/{id}/edit", "JobController@updateGet");
}