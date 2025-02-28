<?php
namespace SGS\Lib;

class Config {
    private static array $config = [];

    /**
     * Load the configuration file.
     */
    public static function load(string $configFile): void {
        if (!file_exists($configFile)) {
            throw new \Exception("Configuration file not found: $configFile");
        }
        self::$config = require $configFile;
    }

    /**
     * Get a configuration value by key (supports environment variables).
     */
    public static function get(string $key, $default = null) {
        // Check environment variables first
        $envKey = strtoupper(str_replace('.', '_', $key));
        $envValue = $_ENV[$envKey] ?? getenv($envKey);

        if ($envValue !== false && $envValue !== null) {
            // Attempt to parse as JSON (for nested arrays/objects)
            $jsonValue = json_decode($envValue, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $jsonValue;
            }

            // Parse comma-separated values into an array
            if (str_contains($envValue, ',')) {
                return array_map('trim', explode(',', $envValue));
            }

            return $envValue;
        }

        // Fallback to config file
        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * Check if a configuration key exists.
     */
    public static function has(string $key): bool {
        return self::get($key) !== null;
    }
}
