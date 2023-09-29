<?php

namespace Utilities;

use Utilities\Logger;
use Utilities\ErrorHandler;

/**
 * Class SessionManager
 *
 * Handles session operations including starting, setting, getting,
 * destroying sessions, and additional features like session timeout,
 * ID regeneration, secure cookies, flash messages, and user authentication data.
 *
 * @category Utilities
 * @package  AkefNetwork
 * @author   Brahim Akef <b@akef.net>
 * @link     https://github.com/akefnetwork
 */
class SessionManager implements \Utilities\Contracts\SessionManagerInterface
{
    protected static $instance;
    const SESSION_TIMEOUT = 1800; // 30 minutes

    private function __construct()
    {
        // Initialize SessionManager if needed.
    }

    public static function getInstance(): self
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

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

    // ... (other methods remain the same with enhancements applied) ...

    public static function getFlash(string $key)
    {
        $message = self::get('flash_' . $key);
        unset($_SESSION['flash_' . $key]);
        return $message;
    }
}
