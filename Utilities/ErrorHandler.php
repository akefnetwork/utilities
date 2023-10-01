<?php

namespace Utilities;

use Utilities\Contracts\ErrorHandlerInterface;
use Throwable;

/**
 * ErrorHandler Utility
 *
 * This utility is responsible for handling errors and exceptions in the application.
 * It logs the errors using the Logger utility and can display user-friendly messages
 * based on the application mode (debug/production).
 *
 * @category Utilities
 * @package  AkefNetwork
 * @author   Brahim Akef <b@akef.net>
 * @link     https://github.com/akefnetwork
 */
class ErrorHandler implements ErrorHandlerInterface
{
    protected static $instance;

    private function __construct()
    {
        // Initialize ErrorHandler if needed.
    }

    /**
     * Retrieve the single instance of ErrorHandler. If it doesn't exist, it's created.
     *
     * @return ErrorHandler The unique ErrorHandler instance.
     */
    public static function getInstance(): self
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Set a custom ErrorHandler instance, typically for testing purposes.
     *
     * This method allows for an external ErrorHandler instance (usually a mock) to be set.
     * This is especially useful during unit testing where the real ErrorHandler instance isn't needed,
     * and a mock or stub should be used instead.
     *
     * @param ErrorHandler|null $instance The ErrorHandler instance to set, or null to reset.
     */
    public static function setInstance(?self $instance): void
    {
        static::$instance = $instance;
    }

    public function handleError(string $messageKey, array $context = []): void
    {
        Logger::getInstance()->log($messageKey, 'error', $context);
        
        // Decide how to respond to the user based on application mode (debug/production).
        // Display a user-friendly error message or redirect to an error page.
        // Use configuration settings and LocaleHandler for localized messages.
    }

    public function handleException(Throwable $exception): void
    {
        $messageKey = 'exception.unhandled';
        $context = [
            'exceptionClass' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ];
        
        $this->handleError($messageKey, $context);
        
        // Similar to handleError, decide how to respond to the user based on application mode.
    }
}
