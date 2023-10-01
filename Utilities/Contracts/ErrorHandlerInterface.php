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

    /**
     * Retrieve the single instance of ErrorHandler. If it doesn't exist, it's created.
     * Note: Static methods in interfaces are not common in PHP and this is for documentation purposes.
     *
     * @return self The unique ErrorHandler instance.
     */
    public static function getInstance(): self;

    /**
     * Set a custom ErrorHandler instance, typically for testing purposes.
     * Note: Static methods in interfaces are not common in PHP and this is for documentation purposes.
     *
     * @param self|null $instance The ErrorHandler instance to set, or null to reset.
     */
    public static function setInstance(?self $instance): void;
}
