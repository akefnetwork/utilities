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

    public static function getInstance(): self
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
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
