<?php

/**
 * Application Configuration File
 * 
 * This file is used to store various configuration settings for the application.
 * It includes settings related to the application environment, debugging,
 * URL, locale, timezone, and utility paths.
 *
 * @category Configuration
 * @package  AkefNetwork
 * @author   Brahim Akef <b@akef.net>
 * @link     http://github.com/akefnetwork
 */

// Define the application root directory.
define('APP_ROOT', dirname(__DIR__));

// Check if the env function exists, if not, define it.
if (!function_exists('env')) {
    function env($key, $default = null) {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
        return $value;
    }
}

return [
    // ... (other configurations remain the same) ...

    /**
     * --------------------------------------------------------------------------
     * Session Configuration
     * --------------------------------------------------------------------------
     * 
     * Here you may define all of the session settings for your application including
     * session timeout, cookie settings, and others.
     * 
     */
    'session' => [
        'timeout' => env('SESSION_TIMEOUT', 1800), // 30 minutes
        'cookie_httponly' => true,
        'cookie_secure' => env('SESSION_COOKIE_SECURE', false), // Set to true if using HTTPS
    ],

    /**
     * --------------------------------------------------------------------------
     * Error Handling Configuration
     * --------------------------------------------------------------------------
     * 
     * Configuration settings related to how errors and exceptions are handled
     * in your application. This includes log settings, display settings, etc.
     * 
     */
    'error_handling' => [
        'display_errors' => env('DISPLAY_ERRORS', false),
        // ... other error handling configurations ...
    ],

    /**
     * --------------------------------------------------------------------------
     * Available Locales
     * --------------------------------------------------------------------------
     * 
     * All available locales for the application. You should update this array
     * with any additional locales your application will support. Ensure that
     * the application logic is implemented to handle these locales correctly.
     */
    'available_locales' => ['en', 'es', 'fr'],

    /**
     * --------------------------------------------------------------------------
     * Utility Paths
     * --------------------------------------------------------------------------
     * 
     * This section defines the absolute file paths to various utility classes
     * used throughout the application. Ensure that all parts of your
     * application that need these paths import them from this configuration file.
     * 
     * Each utility class, such as ErrorHandler, Logger, etc., has its own
     * designated path entry, making it easy to reference them individually
     * wherever needed.
     * 
     */
    'paths' => [
        'errorHandler' => APP_ROOT . '/Utilities/ErrorHandler.php',
        'logger' => APP_ROOT . '/Utilities/Logger.php',
        'localeHandler' => APP_ROOT . '/Utilities/LocaleHandler.php',
        'uploadDir' => APP_ROOT . '/uploads', // Ensure correct permissions are set for this directory.
        // ... additional utility paths ...
    ],

    /**
     * --------------------------------------------------------------------------
     * Localization Configuration
     * --------------------------------------------------------------------------
     * 
     * Configuration settings related to localization and internationalization 
     * of the application. This includes the available locales, default locale, 
     * and the path to the translations files.
     * 
     */
    'localization' => [
        'available_locales' => ['en', 'es', 'fr'],
        'default_locale' => env('APP_LOCALE', 'en'),
        'translations_path' => APP_ROOT . '/lang', // Ensure this directory exists and contains translation files.
    ],
    
    'logFilePath' => '../Logs/app.log',

    // ... other configurations ...
];
