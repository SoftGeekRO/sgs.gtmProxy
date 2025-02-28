<?php
namespace SGS;

use SGS\Lib\ErrorHandler;
use SGS\Lib\Router;
use SGS\Providers\AppServiceProvider;

class Application {
    private Router $router;

    public function __construct(Router $router) {
        $this->router = $router;
    }

    public function run(): void {

        // Register global error handler
        ErrorHandler::register();

        // Bootstrap the application
        $serviceProvider = new AppServiceProvider($this->router);
        $serviceProvider->boot();

        // Dispatch the request
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->router->dispatch($uri);
    }
}