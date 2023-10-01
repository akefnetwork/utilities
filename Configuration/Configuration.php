<?php

namespace Configuration;

/**
 * Application Configuration Class
 * 
 * This class is responsible for managing various configuration settings for 
 * the application. It includes settings related to the application environment, 
 * debugging, URL, locale, timezone, and utility paths.
 *
 * @category Configuration
 * @package  AkefNetwork
 * @author   Brahim Akef <b@akef.net>
 * @link     http://github.com/akefnetwork
 */
class Configuration {

    // Define the application root directory as a static property.
    private static $APP_ROOT;

    /**
     * All application configurations are stored in this array.
     * We'll lazily load the configurations using the getConfigurations method.
     */
    private static $configurations;

    /**
     * Retrieve a configuration value by its key.
     * 
     * @param string $key The key of the configuration to retrieve.
     * @param mixed $default The default value to return if the key isn't found.
     * @return mixed The configuration value.
     */
    public static function get($key, $default = null) {
        $configs = self::getConfigurations();
        return $configs[$key] ?? $default;
    }

    /**
     * Load and return the configurations.
     * 
     * @return array The configurations array.
     */
    private static function getConfigurations() {
        if (self::$configurations) {
            return self::$configurations;
        }

        // Set the APP_ROOT value if it's not set.
        if (!self::$APP_ROOT) {
            self::$APP_ROOT = dirname(__DIR__);
        }

        // Define your configurations here.
        self::$configurations = [
            'session' => [
                'timeout' => self::env('SESSION_TIMEOUT', 1800),
                'cookie_httponly' => true,
                'cookie_secure' => self::env('SESSION_COOKIE_SECURE', false)
            ],
            'error_handling' => [
                'display_errors' => self::env('DISPLAY_ERRORS', false),
                // ... other error handling configurations ...
            ],
            'available_locales' => ['en', 'es', 'fr'],
            'paths' => [
                'errorHandler' => self::$APP_ROOT . '/Utilities/ErrorHandler.php',
                'logger' => self::$APP_ROOT . '/Utilities/Logger.php',
                'localeHandler' => self::$APP_ROOT . '/Utilities/LocaleHandler.php',
                'uploadDir' => self::$APP_ROOT . '/uploads'
            ],
            'localization' => [
                'available_locales' => ['en', 'es', 'fr'],
                'default_locale' => self::env('APP_LOCALE', 'en'),
                'translations_path' => self::$APP_ROOT . '/lang'
            ],
            'logFilePath' => '../Logs/app.log',
            // ... other configurations ...
        ];

        return self::$configurations;
    }

    /**
     * Check if the env function exists, if not, define it.
     * 
     * @param string $key The environment variable key to retrieve.
     * @param mixed $default The default value if the environment variable isn't found.
     * @return mixed The environment variable value.
     */
    private static function env($key, $default = null) {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
        return $value;
    }
}
