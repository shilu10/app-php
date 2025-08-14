<?php

namespace Framework;

use Exception;

class Router
{
    // Arrays to store registered routes for each HTTP method
    protected array $getRoutes = [];
    protected array $postRoutes = [];
    protected array $deleteRoutes = [];
    protected array $patchRoutes = [];

    // Debug mode flag (true = extra debug output)
    protected bool $debug = true;

    /**
     * Constructor to initialize Router
     *
     * @param bool $debug Enable or disable debug mode
     */
    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
    }

    /**
     * Register a GET route
     *
     * @param string $path Route path (e.g. "users/{id}")
     * @param string $controllerHandler Controller@method string
     */
    public function GET(string $path, string $controllerHandler): void
    {
        $this->getRoutes[$path] = $controllerHandler;
    }

    /**
     * Register a POST route
     */
    public function POST(string $path, string $controllerHandler): void
    {
        $this->postRoutes[$path] = $controllerHandler;
    }

    /**
     * Register a PATCH route
     */
    public function PATCH(string $path, string $controllerHandler): void
    {
        $this->patchRoutes[$path] = $controllerHandler;
    }

    /**
     * Register a DELETE route
     */
    public function DELETE(string $path, string $controllerHandler): void
    {
        $this->deleteRoutes[$path] = $controllerHandler;
    }

    /**
     * Listen for incoming HTTP requests and route them
     */
    public function listen(): void
    {
        // Get the request URI path (without query string) and remove leading/trailing slashes
        $uri = trim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), '/');

        // Get the current HTTP method (GET, POST, PATCH, DELETE, etc.)
        $method = $_SERVER["REQUEST_METHOD"];

        // Select the correct set of registered routes based on HTTP method
        $routes = match ($method) {
            "GET"    => $this->getRoutes,
            "POST"   => $this->postRoutes,
            "PATCH"  => $this->patchRoutes,
            "DELETE" => $this->deleteRoutes,
            default  => []
        };

        // Loop through registered routes to find a match
        foreach ($routes as $routePath => $handler) {
            // Remove extra slashes from the route path
            $normalizedRoute = trim($routePath, '/');

            // Convert {param} placeholders into regex capture groups
            $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $normalizedRoute);

            // Check if the request URI matches this route
            if (preg_match("#^{$pattern}$#", $uri, $matches)) {
                // Remove full match from $matches array (first element)
                array_shift($matches);

                // Parameters array to pass to controller
                $params = [];

                if ($method === "GET") {
                    /**
                     * For GET requests:
                     * - Route parameters (from URL)
                     * - Query parameters ($_GET)
                     */
                    $params = array_merge($matches, [$_GET]);
                } 
                elseif ($method === "POST" || $method === "DELETE") {
                    /**
                     * For POST & DELETE requests:
                     * - Route parameters
                     * - Form data ($_POST)
                     */
                    $params = array_merge($matches, [$_POST]);
                } 
                elseif ($method === "PATCH") {
                    // (Debug) Output route parameters if debug is enabled
                    if ($this->debug) {
                        var_dump($matches);
                    }

                    /**
                     * For PATCH requests:
                     * - Read raw request body
                     * - Try to parse as JSON, otherwise as form data
                     */
                    $input = file_get_contents("php://input");
                    $parsedInput = [];

                    // If JSON, decode into array
                    if ($this->isJson($input)) {
                        $parsedInput = json_decode($input, true);
                    } 
                    // Otherwise parse as query string
                    else {
                        parse_str($input, $parsedInput);
                    }

                    /**
                     * Merge:
                     * - Route params
                     * - PATCH body data
                     * - Query parameters ($_GET)
                     */
                    $params = array_merge($matches, [$parsedInput, $_GET]);
                }

                // Dispatch the request to the matched controller method
                $this->callHandler($handler, $params);
                return;
            }
        }

        // No matching route → 404 Not Found
        http_response_code(404);
        if (function_exists('loadView')) {
            loadView("error");
        } else {
            echo "404 Not Found";
        }
    }

    /**
     * Call the given controller method with parameters
     *
     * @param string $controllerHandler Controller@method format
     * @param array $params Parameters to pass to the method
     */
    protected function callHandler(string $controllerHandler, array $params = []): void
    {
        // Split "Controller@method" into class and method
        [$controllerClass, $controllerMethod] = explode("@", $controllerHandler);

        // Add application namespace to controller
        $controllerClass = "App\\Controllers\\" . $controllerClass;

        // Ensure controller class exists
        if (!class_exists($controllerClass)) {
            throw new Exception("Controller {$controllerClass} not found");
        }

        // Instantiate the controller
        $controller = new $controllerClass();

        // Ensure the method exists on the controller
        if (!method_exists($controller, $controllerMethod)) {
            throw new Exception("Method {$controllerMethod} not found in {$controllerClass}");
        }

        // (Debug) Output method name and parameters
        if ($this->debug) {
            var_dump($controllerMethod, $params);
            var_dump($controller);
        }

        // Call the method with the provided parameters
        call_user_func_array([$controller, $controllerMethod], $params);
    }

    /**
     * Check if a given string is valid JSON
     *
     * @param string $string Input string
     * @return bool True if valid JSON, false otherwise
     */
    protected function isJson(string $string): bool
    {
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }
}
