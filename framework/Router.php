<?php

namespace Framework;

use Exception;

/**
 * Router
 * ------
 * - Supports GET, POST, PUT, PATCH, DELETE
 * - Matches routes with {param} placeholders
 * - Extracts path, query, and body parameters
 * - Supports middleware chaining with $next
 */
class Router
{
    // Routes storage, grouped by HTTP method
    protected array $routes = [
        "GET"    => [],
        "POST"   => [],
        "PUT"    => [],
        "PATCH"  => [],
        "DELETE" => [],
    ];

    protected bool $debug = false;

    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
    }

    // -----------------------------
    // Route registration methods
    // -----------------------------
    // Each HTTP method stores a route path, its controller handler, and middleware list

    public function GET(string $path, string $controllerHandler, array $middlewares = []): void
    {
        $this->routes["GET"][$path] = compact("controllerHandler", "middlewares");
    }

    public function POST(string $path, string $controllerHandler, array $middlewares = []): void
    {
        $this->routes["POST"][$path] = compact("controllerHandler", "middlewares");
    }

    public function PUT(string $path, string $controllerHandler, array $middlewares = []): void
    {
        $this->routes["PUT"][$path] = compact("controllerHandler", "middlewares");
    }

    public function PATCH(string $path, string $controllerHandler, array $middlewares = []): void
    {
        $this->routes["PATCH"][$path] = compact("controllerHandler", "middlewares");
    }

    public function DELETE(string $path, string $controllerHandler, array $middlewares = []): void
    {
        $this->routes["DELETE"][$path] = compact("controllerHandler", "middlewares");
    }

    // -----------------------------
    // Main dispatcher
    // -----------------------------
    public function listen(): void
    {
        try {
            // Get URI (without query string) and HTTP method
            $uri = trim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), '/');
            $method = $_SERVER["REQUEST_METHOD"];

            // Get routes for this method
            $routes = $this->routes[$method] ?? [];

            foreach ($routes as $routePath => $handlers) {
                $normalizedRoute = trim($routePath, '/');

                // Extract param names from path (e.g., {id})
                preg_match_all('/\{([^}]+)\}/', $normalizedRoute, $paramNames);
                $paramNames = $paramNames[1] ?? [];

                // Convert {param} placeholders into regex wildcards
                $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $normalizedRoute);

                // If URI matches this route
                if (preg_match("#^{$pattern}$#", $uri, $matches)) {
                    array_shift($matches); // remove full match

                    // Map extracted values to param names
                    $pathParams = [];
                    foreach ($paramNames as $i => $name) {
                        $pathParams[$name] = $matches[$i] ?? null;
                    }

                    // Build unified params object
                    $params = [
                        'pathParams'  => $pathParams,    // values from URL path
                        'queryParams' => $_GET,          // query string (?key=value)
                        'body'        => $this->parseBody($method), // request body
                    ];

                    // Debugging output
                    if ($this->debug) {
                        echo "<pre>Matched route: {$routePath}\n";
                        print_r($params);
                        echo "</pre>";
                    }

                    // Run middleware chain, then controller
                    $this->runMiddlewares(
                        $handlers["middlewares"],
                        $params,
                        fn($params) => $this->callHandler($handlers["controllerHandler"], $params)
                    );
                    return;
                }
            }

            // No matching route → return 404
            http_response_code(404);
            if (function_exists('loadView')) {
                loadView("error");
            } else {
                echo "404 Not Found";
            }

        } catch (Exception $e) {
            // Catch router errors → 500 response
            http_response_code(500);
            if ($this->debug) {
                echo "Router Error: " . $e->getMessage();
            } else {
                echo "500 Internal Server Error";
            }
        }
    }

    // -----------------------------
    // Call the matched controller
    // -----------------------------
    protected function callHandler(string $controllerHandler, array $params = []): void
    {
        // Split "Controller@method"
        [$controllerClass, $controllerMethod] = explode("@", $controllerHandler);
        $controllerClass = "App\\Controllers\\" . $controllerClass;

        // Ensure class exists
        if (!class_exists($controllerClass)) {
            throw new Exception("Controller {$controllerClass} not found");
        }

        $controller = new $controllerClass();

        // Ensure method exists
        if (!method_exists($controller, $controllerMethod)) {
            throw new Exception("Method {$controllerMethod} not found in {$controllerClass}");
        }

        // Debug logging
        if ($this->debug) {
            echo "<pre>Calling {$controllerClass}::{$controllerMethod}()</pre>";
        }

        // Call the controller method with $params
        $controller->$controllerMethod($params);
    }

    // -----------------------------
    // Middleware pipeline
    // -----------------------------
    protected function runMiddlewares(array $middlewares, array $params, callable $controller): void
    {
        // Reduce middlewares into a pipeline that wraps $next
        $pipeline = array_reduce(
            array_reverse($middlewares), // reversed so they run in order
            fn($next, $middleware) => fn($params) => $this->executeMiddleware($middleware, $params, $next),
            $controller // final callable is the controller
        );

      //  var_dump($pipeline);

        // Start execution
        $pipeline($params);
    }

    protected function executeMiddleware(string $middleware, array $params, callable $next): void
    {
        $middlewareClass = "App\\Middlewares\\" . $middleware;

        // Ensure middleware class exists
        if (!class_exists($middlewareClass)) {
            throw new Exception("Middleware {$middlewareClass} not found");
        }

        $instance = new $middlewareClass();

        // Ensure middleware implements handle() method
        if (!method_exists($instance, 'handle')) {
            throw new Exception("Middleware {$middlewareClass} must implement handle() method");
        }

        // Run the middleware, passing $params and $next callback
        $instance->handle($params, $next);
    }

    // -----------------------------
    // Parse request body
    // -----------------------------
    protected function parseBody(string $method): array
    {
        $bodyData = [];

        // Only parse body for these methods
        if (in_array($method, ["POST", "PATCH", "PUT", "DELETE"])) {
            $rawBody = file_get_contents("php://input");

            // Handle JSON or URL-encoded bodies
            if (!empty($rawBody)) {
                if ($this->isJson($rawBody)) {
                    $bodyData = json_decode($rawBody, true);
                } else {
                    parse_str($rawBody, $bodyData);
                }
            }

            // Merge with $_POST (form data)
            if (!empty($_POST)) {
                $bodyData = array_merge($bodyData, $_POST);
            }
        }

        return $bodyData;
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
