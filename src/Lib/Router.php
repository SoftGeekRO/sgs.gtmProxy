<?php
namespace SGS\Lib;

class Router {
    private array $routes = [];
    private array $middleware = [];

    /**
     * Add a route to the router.
     */
    public function addRoute(string $path, string $controller, string $method): void
    {
        $this->routes[$path] = ['controller' => $controller, 'method' => $method];
    }

    /**
     * Add middleware to the router.
     */
    public function addMiddleware(callable $middleware): void
    {
        $this->middleware[] = $middleware;
    }

    /**
     * Dispatch the request to the appropriate controller and method.
     */
    public function dispatch(string $uri): void
    {
        try {
            // Run middleware before handling the request
            $this->runMiddleware();

            if (array_key_exists($uri, $this->routes)) {
                $controller = $this->routes[$uri]['controller'];
                $method = $this->routes[$uri]['method'];

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
            $this->handleError($e);
        }
    }

    /**
     * Run all registered middleware.
     */
    private function runMiddleware(): void
    {
        foreach ($this->middleware as $middleware) {
            call_user_func($middleware);
        }
    }

    /**
     * Handle errors and return a response in JSON or HTML format.
     */
    private function handleError(\Exception $e): void
    {
        $statusCode = $e->getCode() ?: 500;
        $message = $e->getMessage();

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