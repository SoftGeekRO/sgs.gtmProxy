<?php
namespace SGS\Lib;

use SGS\Lib\Logger;
use SGS\Lib\Config;

class ErrorHandler {
    /**
     * Register global error and exception handlers.
     */
    public static function register(): void {
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
    }

    /**
     * Handle PHP errors (convert them to exceptions).
     */
    public static function handleError(int $severity, string $message, string $file, int $line): void {
        if (!(error_reporting() & $severity)) {
            return;
        }
        throw new \ErrorException($message, 0, $severity, $file, $line);
    }

    /**
     * Handle exceptions and log them appropriately.
     */
    public static function handleException(\Throwable $e): void {
        $statusCode = $e->getCode() ?: 500;
        $logContext = [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ];

        // Log based on error severity
        if ($e instanceof \ErrorException) {
            switch ($e->getSeverity()) {
                case E_WARNING:
                case E_USER_WARNING:
                    Logger::warning($e->getMessage(), $logContext);
                    break;
                case E_ERROR:
                case E_USER_ERROR:
                    Logger::fatal($e->getMessage(), $logContext);
                    break;
                default:
                    Logger::error($e->getMessage(), $logContext);
                    break;
            }
        } else {
            Logger::error($e->getMessage(), $logContext);
        }

        // Display error response only if debug mode is enabled
        if (Config::get('debug')) {
            self::displayError($e, $statusCode);
        } else {
            // Log the error and display a generic message
            Logger::error($e->getMessage(), $logContext);
            self::displayGenericError($e, $statusCode);
        }
    }

    /**
     * Display the error in JSON or HTML format.
     */
    private static function displayError(\Throwable $e, int $statusCode): void {
        $acceptHeader = $_SERVER['HTTP_ACCEPT'] ?? 'text/html';
        $isJson = str_contains($acceptHeader, 'application/json');

        if ($isJson) {
            header('Content-Type: application/json');
            http_response_code($statusCode);
            echo json_encode([
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => $statusCode,
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => Config::get('debug') ? $e->getTraceAsString() : null,
                ],
            ]);
        } else {
            header('Content-Type: text/html');
            http_response_code($statusCode);
            echo "<h1>Error $statusCode</h1>";
            echo "<p><strong>Message:</strong> {$e->getMessage()}</p>";
            if (Config::get('debug')) {
                echo "<p><strong>File:</strong> {$e->getFile()}</p>";
                echo "<p><strong>Line:</strong> {$e->getLine()}</p>";
                echo "<pre>{$e->getTraceAsString()}</pre>";
            }
        }
    }

    /**
     * Display a generic error message when debug mode is disabled.
     */
    private static function displayGenericError(\Throwable $e, int $statusCode): void {
        $acceptHeader = $_SERVER['HTTP_ACCEPT'] ?? 'text/html';
        $isJson = str_contains($acceptHeader, 'application/json');

        if ($isJson) {
            header('Content-Type: application/json');
            http_response_code($statusCode);
            echo json_encode([
                'error' => [
                    'message' => 'An error occurred. Please try again later.',
                    'code' => $statusCode,
                ],
            ]);
        } else {
            header('Content-Type: text/html');
            http_response_code($statusCode);
            echo "<h1>Error $statusCode</h1>";
            echo "<p>{$e->getMessage()}</p>";
        }
    }
}