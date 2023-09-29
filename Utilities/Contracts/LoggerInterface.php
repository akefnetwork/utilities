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
}
