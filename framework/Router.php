<?php

namespace Framework;

use Exception;

/**
 * Router
 * ------
 * Matches incoming HTTP requests to registered controller methods.
 * Automatically extracts:
 *   - Path parameters from URI placeholders like /users/{id}
 *   - Query parameters from ?foo=bar
 *   - Body data from POST/PATCH/PUT/DELETE requests (JSON or form-data)
 */
class Router
{
    // Route tables for different HTTP verbs
    protected array $getRoutes    = [];
    protected array $postRoutes   = [];
    protected array $deleteRoutes = [];
    protected array $patchRoutes  = [];

    // Debug flag
    protected bool $debug = false;

    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
    }

    // -----------------------------
    // Route registration methods
    // -----------------------------
    public function GET(string $path, string $controllerHandler): void
    {
        $this->getRoutes[$path] = $controllerHandler;
    }

    public function POST(string $path, string $controllerHandler): void
    {
        $this->postRoutes[$path] = $controllerHandler;
    }

    public function PATCH(string $path, string $controllerHandler): void
    {
        $this->patchRoutes[$path] = $controllerHandler;
    }

    public function PUT(string $path, string $controllerHandler): void
    {
        $this->patchRoutes[$path] = $controllerHandler; // treat PUT same as PATCH
    }

    public function DELETE(string $path, string $controllerHandler): void
    {
        $this->deleteRoutes[$path] = $controllerHandler;
    }

    // -----------------------------
    // Main listener
    // -----------------------------
    public function listen(): void
    {
        // Get request path without query string
        $uri = trim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), '/');

        // HTTP verb (GET, POST, PATCH, PUT, DELETE)
        $method = $_SERVER["REQUEST_METHOD"];

        // Select correct route set
        $routes = match ($method) {
            "GET"    => $this->getRoutes,
            "POST"   => $this->postRoutes,
            "PATCH"  => $this->patchRoutes,
            "PUT"    => $this->patchRoutes,
            "DELETE" => $this->deleteRoutes,
            default  => []
        };

        // Loop over registered routes and match against request URI
        foreach ($routes as $routePath => $handler) {
            $normalizedRoute = trim($routePath, '/');

            // Replace {param} placeholders with regex capture groups
            $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $normalizedRoute);

            // Check if the request URI matches this route pattern
            if (preg_match("#^{$pattern}$#", $uri, $matches)) {
                array_shift($matches); // remove the full match

                // ----------------------------------------
                // Build a clean, consistent params array
                // ----------------------------------------
                $pathParams  = $matches; // from {placeholders}
                $queryParams = $_GET;    // from ?foo=bar
                $bodyData    = [];       // will fill for methods with a body

                // Only read body for POST/PATCH/PUT/DELETE
                if (in_array($method, ["POST", "PATCH", "PUT", "DELETE"])) {
                    $rawBody = file_get_contents("php://input");

                    // If body exists, try JSON first
                    if (!empty($rawBody)) {
                        if ($this->isJson($rawBody)) {
                            $bodyData = json_decode($rawBody, true);
                        } else {
                            // Otherwise treat as form-urlencoded
                            parse_str($rawBody, $bodyData);
                        }
                    }

                    // Merge in $_POST in case of traditional form submissions
                    if (!empty($_POST)) {
                        $bodyData = array_merge($bodyData, $_POST);
                    }
                }

                // Structured parameters passed to controller
                $params = [
                    'pathParams'  => $pathParams,
                    'queryParams' => $queryParams,
                    'body'        => $bodyData
                ];

                // Optional debugging
                if ($this->debug) {
                    echo "<pre>Matched route: {$routePath}\n";
                    print_r($params);
                    echo "</pre>";
                }

                // Call controller method with structured params
                $this->callHandler($handler, $params);
                return;
            }
        }

        // If no match found → 404
        http_response_code(404);
        if (function_exists('loadView')) {
            loadView("error");
        } else {
            echo "404 Not Found";
        }
    }

    // -----------------------------
    // Call the matched controller
    // -----------------------------
    protected function callHandler(string $controllerHandler, array $params = []): void
    {
        // Split into [ControllerClass, methodName]
        [$controllerClass, $controllerMethod] = explode("@", $controllerHandler);

        // Add namespace prefix
        $controllerClass = "App\\Controllers\\" . $controllerClass;

        if (!class_exists($controllerClass)) {
            throw new Exception("Controller {$controllerClass} not found");
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $controllerMethod)) {
            throw new Exception("Method {$controllerMethod} not found in {$controllerClass}");
        }

        // Debug
        if ($this->debug) {
            echo "<pre>Calling {$controllerClass}::{$controllerMethod}()</pre>";
        }

        // Pass the structured array as a single parameter
        $controller->$controllerMethod($params);
    }

    // -----------------------------
    // Utility: check if string is JSON
    // -----------------------------
    protected function isJson(string $string): bool
    {
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }
}
