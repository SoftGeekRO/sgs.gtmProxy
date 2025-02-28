<?php
namespace SGS\Lib;

use SGS\Lib\Config;

class Logger {
    private static $loggers = [];

    /**
     * Log a message with a specific type (info, debug, warning, error, fatal).
     */
    public static function log(string $type, string $message, array $context = []): void
    {
        // Skip logging if debug is false and the log type is not debug, fatal, or error
        if (!Config::get('debug') && !in_array($type, ['debug', 'fatal', 'error'])) {
            return;
        }

        $config = Config::get("logs.$type");
        if (!$config) {
            throw new \InvalidArgumentException("Log type '$type' is not configured.");
        }

        $logEntry = self::formatLogEntry($type, $message, $context, $config['format']);
        file_put_contents($config['file'], $logEntry . PHP_EOL, FILE_APPEND);
    }

    /**
     * Shortcut methods for log types.
     */
    public static function info(string $message, array $context = []): void {
        self::log('info', $message, $context);
    }

    public static function debug(string $message, array $context = []): void {
        self::log('debug', $message, $context);
    }

    public static function warning(string $message, array $context = []): void {
        self::log('warning', $message, $context);
    }

    public static function error(string $message, array $context = []): void {
        self::log('error', $message, $context);
    }

    public static function fatal(string $message, array $context = []): void {
        self::log('fatal', $message, $context);
    }

    /**
     * Format the log entry based on the log type's configured format.
     */
    private static function formatLogEntry(string $type, string $message, array $context, string $format): string {
        $timestamp = date('Y-m-d H:i:s');
        $logData = [
            'timestamp' => $timestamp,
            'type' => $type,
            'message' => $message,
            'context' => $context,
        ];

        return match ($format) {
            'json' => json_encode($logData),
            default => "[$timestamp] [$type] $message - " . json_encode($context),
        };
    }
}