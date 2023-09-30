<?php

namespace Utilities;

use Utilities\Contracts\SessionManagerInterface;

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
        // ... [existing code]
    }

    /**
     * Sets a session variable.
     *
     * @param string $key The session variable name.
     * @param mixed $value The value to set.
     * @return void
     */
    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Retrieves a session variable.
     *
     * @param string $key The session variable name.
     * @return mixed|null Returns the session variable value, or null if not set.
     */
    public static function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Destroys the session.
     *
     * @return void
     */
    public static function destroySession(): void
    {
        session_destroy();
    }

    /**
     * Sets a flash message in the session.
     *
     * @param string $key The flash message key.
     * @param string $message The message content.
     * @return void
     */
    public static function setFlash(string $key, $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Retrieves a flash message from the session.
     *
     * @param string $key The flash message key.
     * @return string|null Returns the flash message, or null if not set.
     */
    public static function getFlash(string $key)
    {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }

    /**
     * Stores user authentication data in the session.
     *
     * @param mixed $userData The user authentication data.
     * @return void
     */
    public static function setUserAuthData($userData): void
    {
        $_SESSION['user_auth_data'] = $userData;
    }

    /**
     * Retrieves user authentication data from the session.
     *
     * @return mixed|null Returns the user authentication data, or null if not set.
     */
    public static function getUserAuthData()
    {
        return $_SESSION['user_auth_data'] ?? null;
    }

    /**
     * Sets user's preferred locale in the session.
     *
     * @param string $locale Locale code (e.g., "en_US").
     * @return void
     */
    public static function setUserPreferredLocale(string $locale): void
    {
        self::set('preferred_locale', $locale);
    }

    /**
     * Retrieves user's preferred locale from the session.
     *
     * @return string|null Returns the preferred locale, or null if not set.
     */
    public static function getUserPreferredLocale(): ?string
    {
        return self::get('preferred_locale');
    }
}
