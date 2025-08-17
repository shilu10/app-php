<?php 

function registerListingRoutes($router) {
    $router->GET("/listings", "ListingController@listAll", ["Auth"]);
}

function registerHomeRoutes($router) {
    $router->GET("/", "HomeController@home");
}

function registerJobRoutes($router) {
    $router->GET("jobs/details/{id}", "JobController@getDetails", ["Auth"]);
    $router->DELETE("jobs/{id}", "JobController@delete", ["Auth"]);
    $router->POST("jobs/", "JobController@createPost", ["Auth"]);
    $router->GET("jobs/", "JobController@createGet", ["Auth"]);
    $router->PATCH("jobs/{id}/edit", "JobController@update", ["Auth"]);
    $router->GET("jobs/{id}/edit", "JobController@updateGet", ["Auth"]);
}

function registerUserroutes($router) {
    $router->POST("users/login", "UserController@login");
    $router->POST("users/register", "UserController@register");
    $router->GET("users/login", "UserController@loginGet");
    $router->GET("users/register", "UserController@registerGet");
    $router->GET("users/logout", "UserController@logout");
    $router->GET("users/profile", "UserController@profile");
}