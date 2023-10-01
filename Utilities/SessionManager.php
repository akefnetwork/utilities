<?php

namespace Utilities;

use Utilities\Contracts\SessionManagerInterface;
use Utilities\Logger;
use Utilities\ErrorHandler;

/**
 * Class SessionManager
 *
 * The SessionManager class provides a comprehensive session management solution.
 * It facilitates the creation, retrieval, and manipulation of session data,
 * including special functionalities like flash messages and user authentication data storage.
 *
 * The class uses the Singleton pattern to ensure only a single instance exists across the application,
 * thereby maintaining consistent session management.
 *
 * Features:
 * - Secure session configurations to ensure data privacy.
 * - Session regeneration logic for improved security against fixation attacks.
 * - Flash message handling for transient notifications.
 * - User authentication data storage for user-specific session requirements.
 *
 * Dependencies:
 * - Logger: To log important session-related events.
 * - ErrorHandler: To gracefully handle session-related errors.
 *
 * Usage:
 * $sessionManager = SessionManager::getInstance();
 * $sessionManager->set('key', 'value');
 * echo $sessionManager->get('key');
 *
 * @category Utilities
 * @package  AkefNetwork
 * @author   Brahim Akef <b@akef.net>
 * @link     https://github.com/akefnetwork
 */
class SessionManager implements SessionManagerInterface
{
    /**
     * @var SessionManager Holds the single instance of the SessionManager class.
     */
    private static $instance;

    /**
     * Session timeout duration in seconds. 1800 seconds equates to 30 minutes.
     *
     * @const int
     */
    const SESSION_TIMEOUT = 1800;

    /**
     * Private constructor to ensure only one instance is created.
     *
     * Automatically starts the session upon instantiation.
     */
    private function __construct()
    {
        $this->startSession();
    }

    /**
     * Provides a single instance of SessionManager.
     *
     * Ensures only one instance of SessionManager is created.
     *
     * @return SessionManager The single instance of the SessionManager class.
     */
    public static function getInstance(): self
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Start the session with secure configurations.
     *
     * @return void
     */
    public function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Set secure session configurations.
            ini_set('session.cookie_httponly', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? '1' : '0');

            if (!session_start()) {
                Logger::getInstance()->log('session.start_failure', 'error');
                ErrorHandler::handleError('session.start_failure');
                return;
            }

            // Handle session timeouts and regenerate IDs for security.
            if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > self::SESSION_TIMEOUT)) {
                self::destroySession();
                Logger::getInstance()->log('session.timed_out', 'warning');
            }
            $_SESSION['LAST_ACTIVITY'] = time();
            session_regenerate_id(true);
            Logger::getInstance()->log('session.start_success', 'info');
        }
    }

    /**
     * Set a session variable.
     *
     * @param string $key The session variable name.
     * @param mixed $value The value to store.
     * @return void
     */
    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Retrieve a session variable.
     *
     * @param string $key The session variable name.
     * @return mixed|null The session variable value or null if not set.
     */
    public function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * End the current session and destroy all data.
     *
     * @return void
     */
    public function destroySession(): void
    {
        session_destroy();
        Logger::getInstance()->log('session.destroyed', 'info');
    }

    /**
     * Set a flash message for one-time display.
     *
     * @param string $key The flash message key.
     * @param string $message The message content.
     * @return void
     */
    public function setFlash(string $key, string $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Retrieve a flash message.
     *
     * @param string $key The flash message key.
     * @return string|null The flash message or null if not found.
     */
    public function getFlash(string $key): ?string
    {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }

    /**
     * Store user authentication data in the session.
     *
     * @param mixed $userData The user data.
     * @return void
     */
    public function setUserAuthData($userData): void
    {
        $_SESSION['user_auth_data'] = $userData;
    }

    /**
     * Retrieve user authentication data from the session.
     *
     * @return mixed|null The user data or null if not set.
     */
    public function getUserAuthData()
    {
        return $_SESSION['user_auth_data'] ?? null;
    }

    /**
     * Set the user's preferred locale in the session.
     *
     * @param string $locale The locale code.
     * @return void
     */
    public function setUserPreferredLocale(string $locale): void
    {
        $this->set('preferred_locale', $locale);
    }

    /**
     * Get the user's preferred locale from the session.
     *
     * @return string|null The locale code or null if not set.
     */
    public function getUserPreferredLocale(): ?string
    {
        return $this->get('preferred_locale');
    }
}
