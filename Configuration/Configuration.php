<?php

namespace Configuration;

/**
 * Application Configuration Class
 * 
 * This class is used to store and retrieve various configuration settings for the application.
 * It includes settings related to the application environment, debugging,
 * URL, locale, timezone, and utility paths.
 *
 * @category Configuration
 * @package  AkefNetwork
 * @author   Brahim Akef <b@akef.net>
 * @link     http://github.com/akefnetwork
 */
class Configuration {

    // Define the application root directory.
    private const APP_ROOT = dirname(__DIR__);

    /**
     * All application configurations are stored in this array.
     */
    private static $configurations = [
        // ... (all your configurations) ...

        'session' => [
            'timeout' => self::env('SESSION_TIMEOUT', 1800), // 30 minutes
            'cookie_httponly' => true,
            'cookie_secure' => self::env('SESSION_COOKIE_SECURE', false), // Set to true if using HTTPS
        ],

        'error_handling' => [
            'display_errors' => self::env('DISPLAY_ERRORS', false),
            // ... other error handling configurations ...
        ],

        'available_locales' => ['en', 'es', 'fr'],

        'paths' => [
            'errorHandler' => self::APP_ROOT . '/Utilities/ErrorHandler.php',
            'logger' => self::APP_ROOT . '/Utilities/Logger.php',
            'localeHandler' => self::APP_ROOT . '/Utilities/LocaleHandler.php',
            'uploadDir' => self::APP_ROOT . '/uploads', // Ensure correct permissions are set for this directory.
            // ... additional utility paths ...
        ],

        'localization' => [
            'available_locales' => ['en', 'es', 'fr'],
            'default_locale' => self::env('APP_LOCALE', 'en'),
            'translations_path' => self::APP_ROOT . '/lang', // Ensure this directory exists and contains translation files.
        ],

        'logFilePath' => '../Logs/app.log',

        // ... other configurations ...
    ];

    /**
     * Retrieve a configuration value by its key.
     * 
     * @param string $key The key of the configuration to retrieve.
     * @param mixed $default The default value to return if the key isn't found.
     * @return mixed The configuration value.
     */
    public static function get($key, $default = null) {
        return static::$configurations[$key] ?? $default;
    }

    /**
     * Utility function to retrieve environment variables or return a default value.
     * 
     * @param string $key The key of the environment variable to retrieve.
     * @param mixed $default The default value to return if the environment variable isn't found.
     * @return mixed The value of the environment variable.
     */
    private static function env($key, $default = null) {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
        return $value;
    }
}
