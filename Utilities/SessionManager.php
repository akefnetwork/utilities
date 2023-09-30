<?php

namespace Utilities;

/**
 * SessionManager Class
 *
 * The SessionManager class provides functionalities to manage PHP sessions. 
 * It handles operations such as starting, setting, and retrieving session 
 * variables, including special operations for flash messages and user 
 * authentication data storage. The class also integrates with other utilities 
 * for logging and error handling.
 *
 * @category Utilities
 * @package  AkefNetwork
 * @author   Brahim Akef <b@akef.net>
 * @link     https://github.com/akefnetwork
 */
class SessionManager implements SessionManagerInterface
{
    /**
     * Instance of the SessionManager (Singleton pattern).
     *
     * @var SessionManager
     */
    protected static $instance;

    /**
     * Session timeout duration in seconds.
     * 1800 seconds equates to 30 minutes.
     *
     * @const int
     */
    const SESSION_TIMEOUT = 1800;

    /**
     * Private constructor for Singleton pattern.
     */
    private function __construct()
    {
        // Initialize SessionManager if required.
    }

    /**
     * Provides a single instance of SessionManager.
     *
     * Ensures only one instance of SessionManager is created.
     *
     * @return SessionManager
     */
    public static function getInstance(): self
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Starts the session if it isn't already started.
     *
     * This method handles session timeout and session ID regeneration for 
     * added security. It also sets secure session cookie parameters.
     *
     * @return void
     * @throws \Exception If session_start() fails.
     */
    public static function startSession(): void
    {
        try {
            if (session_status() == PHP_SESSION_NONE) {
                // Secure session cookie parameters.
                ini_set('session.cookie_httponly', '1');
                ini_set('session.cookie_secure', stristr($_SERVER['SERVER_PROTOCOL'], 'https/') ? '1' : '0');
                ini_set('session.use_only_cookies', '1');

                session_start();

                // Handle session timeout.
                if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > self::SESSION_TIMEOUT)) {
                    self::destroySession();
                }
                $_SESSION['LAST_ACTIVITY'] = time();

                // Regenerate session ID.
                session_regenerate_id(true);

                Logger::getInstance()->log('session.start_success', 'info');
            }
        } catch (\Exception $e) {
            ErrorHandler::handleException($e);
        }
    }

    // Other existing methods ...

    /**
     * Sets user's preferred locale in the session.
     *
     * @param string $locale Locale code (e.g., "en_US").
     * @return void
     */
    public static function setUserPreferredLocale(string $locale): void
    {
        self::set('preferred_locale', $locale);
        Logger::getInstance()->log('session.set_preferred_locale', 'info', ['locale' => $locale]);
    }

    /**
     * Retrieves user's preferred locale from the session.
     *
     * @return string|null Returns the preferred locale, or null if not set.
     */
    public static function getUserPreferredLocale(): ?string
    {
        $locale = self::get('preferred_locale');

        if (!$locale) {
            Logger::getInstance()->log('session.get_preferred_locale_missing', 'warning');
        } else {
            Logger::getInstance()->log('session.get_preferred_locale', 'info', ['locale' => $locale]);
        }

        return $locale;
    }
}
