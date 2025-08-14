<?php 
    require __DIR__ . '/../vendor/autoload.php';
    use Framework\Router;

    require "../helper.php";
    require basePath("routes/routes.php");
    

    $router = new Router();

    registerListingRoutes($router);
    registerHomeRoutes($router);
    registerJobRoutes($router);

    $router->listen();
?>
