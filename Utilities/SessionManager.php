<?php

namespace Utilities;

use Utilities\Contracts\SessionManagerInterface;
use Utilities\Logger;
use Utilities\ErrorHandler;

/**
 * Class SessionManager
 *
 * The SessionManager class is responsible for managing session operations,
 * including starting, setting, getting, and destroying sessions. It also
 * handles additional features like session timeout, ID regeneration, secure
 * cookies, flash messages, and user authentication data storage.
 *
 * @category Utilities
 * @package  AkefNetwork
 * @author   Brahim Akef <b@akef.net>
 * @link     https://github.com/akefnetwork
 */
class SessionManager implements SessionManagerInterface
{
    /**
     * Instance of the SessionManager (Singleton).
     *
     * @var SessionManager
     */
    protected static $instance;

    /**
     * Session timeout duration in seconds.
     * Here, 1800 seconds is equivalent to 30 minutes.
     *
     * @const int
     */
    const SESSION_TIMEOUT = 1800;

    /**
     * SessionManager constructor.
     * Declared private to prevent creating a new instance externally.
     */
    private function __construct()
    {
        // Initialize SessionManager if needed.
    }

    /**
     * Retrieves the instance of the SessionManager.
     * If the instance does not exist, it will be created.
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
     * Starts the session if it is not already started.
     * Also handles session timeout and ID regeneration.
     *
     * @return void
     * @throws \Exception If session_start() fails.
     */
    public static function startSession(): void
    {
        try {
            if (session_status() == PHP_SESSION_NONE) {
                // Set secure session cookie parameters.
                ini_set('session.cookie_httponly', '1');
                ini_set('session.cookie_secure', stristr($_SERVER['SERVER_PROTOCOL'], 'https/') ? '1' : '0');
                ini_set('session.use_only_cookies', '1');

                session_start();

                if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > self::SESSION_TIMEOUT)) {
                    self::destroySession(); // session timed out
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

    /**
     * Sets a session variable.
     *
     * @param string $key The key under which the value is stored in the session.
     * @param mixed $value The value to store in the session.
     * @return void
     */
    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Retrieves a session variable by key.
     *
     * @param string $key The key of the session variable to retrieve.
     * @return mixed|null The value of the session variable, or NULL if not set.
     */
    public static function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Destroys the session, removing all session variables.
     *
     * @return void
     */
    public static function destroySession(): void
    {
        session_unset();
        session_destroy();
    }

    /**
     * Sets a flash message in the session.
     *
     * @param string $key The key under which the message is stored in the session.
     * @param mixed $message The flash message to store in the session.
     * @return void
     */
    public static function setFlash(string $key, $message): void
    {
        $_SESSION['flash_' . $key] = $message;
    }

    /**
     * Retrieves a flash message from the session.
     *
     * @param string $key The key of the flash message to retrieve.
     * @return mixed|null The flash message, or NULL if not set.
     */
    public static function getFlash(string $key)
    {
        $message = self::get('flash_' . $key);
        unset($_SESSION['flash_' . $key]);
        return $message;
    }

    /**
     * Stores user authentication data in the session.
     *
     * @param mixed $userData The user authentication data to store in the session.
     * @return void
     */
    public static function setUserAuthData($userData): void
    {
        $_SESSION['user_auth_data'] = $userData;
    }

    /**
     * Retrieves user authentication data from the session.
     *
     * @return mixed|null The user authentication data, or NULL if not set.
     */
    public static function getUserAuthData()
    {
        return $_SESSION['user_auth_data'] ?? null;
    }
}
