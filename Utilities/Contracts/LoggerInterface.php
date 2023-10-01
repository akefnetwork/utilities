<?php

namespace Utilities\Contracts;

/**
 * Interface LoggerInterface
 *
 * LoggerInterface defines the contract for the Logger class. Any class implementing
 * this interface must provide an implementation for the log method, ensuring consistent
 * logging across different logger implementations.
 *
 * @category Utilities
 * @package  AkefNetwork
 * @author   Brahim Akef <b@akef.net>
 * @link     https://github.com/akefnetwork
 */
interface LoggerInterface
{
    /**
     * Log a message with a specific log level and context.
     *
     * This method should be used to log messages with different log levels. It should
     * format the log entry, open the log file, write the entry, and close the file. If any
     * error occurs during this process, it should be appropriately handled.
     *
     * @param string $message The locale key of the message to be logged.
     * @param string $level The level of log (e.g., 'info', 'error').
     * @param array $context Additional context information (e.g., 'module', 'function').
     * @return bool Returns true if the log entry was successfully written, false otherwise.
     */
    public function log(string $message, string $level, array $context = []): bool;

    /**
     * Retrieve the single instance of Logger. If it doesn't exist, it's created.
     * Note: Static methods in interfaces are not common in PHP and this is for documentation purposes.
     *
     * @return self The unique Logger instance.
     */
    public static function getInstance(): self;

    /**
     * Set a custom Logger instance, typically for testing purposes.
     * Note: Static methods in interfaces are not common in PHP and this is for documentation purposes.
     *
     * @param self|null $instance The Logger instance to set, or null to reset.
     */
    public static function setInstance(?LoggerInterface $instance): void;

    /**
     * Set configuration for Logger dependencies.
     * Note: Static methods in interfaces are not common in PHP and this is for documentation purposes.
     *
     * @param $sessionManager The SessionManager instance.
     * @param $errorHandler The ErrorHandler instance.
     * @param string $logFilePath Path to the log file.
     */
    public static function configure(\Utilities\SessionManager $sessionManager, \Utilities\ErrorHandler $errorHandler): void;
}