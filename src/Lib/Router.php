<?php
namespace SGS\Lib;

use SGS\Middleware\ErrorMiddleware;

class Router {
    private array $routes = [];
    private array $middleware = [];

    private \SGS\Lib\Logger $logger;

    /**
     * @throws \Exception
     */
    function __construct() {

        // Initialize the logger
        $this->logger = new Logger(Config::get('log_file'), Config::get('log_format'));
    }

    /**
     * Set the logger.
     */
    public function setLogger(Logger $logger): void {
        $this->logger = $logger;
    }

    /**
     * Get the logger.
     */
    public function getLogger(): Logger {
        return $this->logger;
    }

    /**
     * Add a route to the router.
     */
    public function addRoute(string $path, string $controller, string $method, array $middleware = []): void {
        $this->routes[$path] = [
            'controller' => $controller,
            'method' => $method,
            'middleware' => $middleware,
        ];
    }

    /**
     * Add middleware to the router.
     */
    public function addMiddleware(callable $middleware): void {
        $this->middleware[] = $middleware;
    }

    /**
     * Dispatch the request to the appropriate controller and method.
     */
    public function dispatch(string $uri): void {
        try {
            // Run global middleware before handling the request
            $this->runMiddleware();

            if (array_key_exists($uri, $this->routes)) {
                $route = $this->routes[$uri];

                // Run route-specific middleware
                $this->runMiddleware($route['middleware']);

                $controller = $route['controller'];
                $method = $route['method'];

                if (!class_exists($controller)) {
                    throw new \Exception("Controller not found: $controller", 404);
                }

                if (!method_exists($controller, $method)) {
                    throw new \Exception("Method not found: $method in $controller", 404);
                }

                $controllerInstance = new $controller();
                $controllerInstance->$method();
            } else {
                throw new \Exception("Route not found: $uri", 404);
            }
        } catch (\Exception $e) {
            // Pass the exception to the error handler
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Run middleware.
     */
    private function runMiddleware(array $middleware = []): void {
        // Run global middleware
        foreach ($this->middleware as $middleware) {
            call_user_func($middleware);
        }

        // Run route-specific middleware
        foreach ($middleware as $middlewareClass) {
            call_user_func($middleware);
        }
    }

    /**
     * Handle errors and return a response in JSON or HTML format.
     */
    private function handleError(\Exception $e): void {
        $statusCode = $e->getCode() ?: 500;
        $message = $e->getMessage();

        // Log the error
        $this->logger->log($message, [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        // Check the Accept header to determine the response format
        $acceptHeader = $_SERVER['HTTP_ACCEPT'] ?? 'text/html';

        if (str_contains($acceptHeader, 'application/json')) {
            // Return JSON response
            header('Content-Type: application/json');
            http_response_code($statusCode);
            echo json_encode([
                'error' => true,
                'message' => $message,
                'status' => $statusCode,
            ]);
        } else {
            // Return HTML response
            header('Content-Type: text/html');
            http_response_code($statusCode);
            echo "<h1>Error $statusCode</h1>";
            echo "<p>$message</p>";
        }
    }
}