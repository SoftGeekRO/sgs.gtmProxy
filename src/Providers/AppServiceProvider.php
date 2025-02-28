<?php
namespace SGS\Providers;

use SGS\Lib\Logger;
use SGS\Lib\Router;
use SGS\Middleware\ErrorMiddleware;
use SGS\Lib\Config;

class AppServiceProvider {
    private Router $router;

    public function __construct(Router $router) {
        $this->router = $router;
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void {
        $this->initializeLogger();
        $this->registerMiddleware();
        $this->registerRoutes();
    }

    /**
     * Initialize the logger.
     */
    private function initializeLogger(): void {
        $logger = new Logger(Config::get('log_file'), Config::get('log_format'));
        $this->router->setLogger($logger);
    }

    /**
     * Register middleware.
     */
    private function registerMiddleware(): void {
        foreach (Config::get('middleware') as $middlewareClass) {
            if ($middlewareClass === ErrorMiddleware::class) {
                $middleware = new $middlewareClass(Config::get('debug'), $this->router->getLogger());
            } else {
                $middleware = new $middlewareClass();
            }
            $this->router->addMiddleware([$middleware, 'handle']);
        }
    }

    /**
     * Register routes.
     */
    private function registerRoutes(): void {
        foreach (Config::get('routes') as $path => $route) {
            $this->router->addRoute($path, $route['controller'], $route['method'], $route['middleware'] ?? []);
        }
    }
}