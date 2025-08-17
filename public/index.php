<?php 
    require __DIR__ . '/../vendor/autoload.php';
    use Framework\Router;
    use App\Controllers\ErrorController;

    require "../helper.php";
    require basePath("routes/routes.php");

    // Register a global exception handler
    set_exception_handler(function ($exception) {
        error_log("Uncaught exception: " . $exception->getMessage());
        ErrorController::serverError("Something went wrong, please try again later.");
    });
    

    $router = new Router();

    registerListingRoutes($router);
    registerHomeRoutes($router);
    registerJobRoutes($router);
    registerUserroutes($router);

    $router->listen();
?>
