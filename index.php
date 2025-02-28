<?php

require 'vendor/autoload.php';

use \Dotenv\Dotenv;
use SGS\Application;
use SGS\Lib\Config;
use SGS\Lib\Router;

$dotenv = Dotenv::createImmutable(__DIR__); // Path to .env directory
$dotenv->load();

// Load configuration
Config::load(__DIR__ . '/config/app.php');

// Create the router
$router = new Router();

$app = new Application($router);
$app->run();

// Flush the output buffer
if (ob_get_level() > 0) {
    ob_end_flush();
}
