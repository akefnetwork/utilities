<?php

namespace Utilities\Contracts;

use Throwable;

/**
 * Interface ErrorHandlerInterface
 *
 * ErrorHandlerInterface defines the contract for the ErrorHandler class. Any class implementing
 * this interface must provide implementations for the handleError and handleException methods,
 * ensuring consistent error handling across different ErrorHandler implementations.
 *
 * @category Utilities
 * @package  AkefNetwork
 * @author   Brahim Akef <b@akef.net>
 * @link     https://github.com/akefnetwork
 */
interface ErrorHandlerInterface
{
    /**
     * Handle an error identified by a message key and context.
     *
     * This method should log the error using the Logger utility and decide how to respond
     * to the user based on application mode (debug/production). In production mode, it should
     * display user-friendly messages and avoid exposing sensitive information.
     *
     * @param string $messageKey The locale key of the error message to be handled.
     * @param array $context Additional context information.
     * @return void
     */
    public function handleError(string $messageKey, array $context = []): void;

    /**
     * Handle an uncaught exception.
     *
     * This method should log the exception details using the Logger utility and decide how to
     * respond to the user based on application mode (debug/production). In production mode, it should
     * display user-friendly messages and avoid exposing sensitive information.
     *
     * @param Throwable $exception The uncaught exception to be handled.
     * @return void
     */
    public function handleException(Throwable $exception): void;
}
