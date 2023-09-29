<?php

namespace Utilities;

use Utilities\Contracts\LoggerInterface;
use Utilities\ErrorHandler;
use Utilities\SessionManager;
use DateTime;

/**
 * Class Logger
 *
 * The Logger class is a utility for logging messages with different log levels.
 * It implements a Singleton pattern to ensure only one instance is used across the application.
 * The Logger class is part of the Utilities namespace and is used throughout the application
 * for consistent logging of messages and events.
 *
 * Dependencies:
 * - SessionManager: Required for retrieving user information from the session.
 * - ErrorHandler: Required for handling errors that might occur during the logging process.
 * - LoggerInterface: The interface that this class implements, defining the contract for the Logger.
 * - DateTime: Used for generating timestamps for the log entries.
 *
 * @category Utilities
 * @package  AkefNetwork
 * @author   Brahim Akef <b@akef.net>
 * @link     https://github.com/akefnetwork
 */
class Logger implements LoggerInterface
{
    /**
     * @var Logger Holds the single instance of the Logger class.
     */
    protected static $instance;

    /**
     * @var string The file path to the log file.
     */
    protected $logFilePath;

    /**
     * @var SessionManager Holds an instance of the SessionManager for retrieving user information.
     */
    protected $sessionManager;

    /**
     * @var ErrorHandler Holds an instance of the ErrorHandler for handling errors during logging.
     */
    protected $errorHandler;

    /**
     * Logger constructor.
     *
     * The constructor is private to prevent creating multiple instances.
     * It initializes the SessionManager, ErrorHandler, and log file path.
     *
     * @param SessionManager $sessionManager An instance of the SessionManager.
     * @param ErrorHandler $errorHandler An instance of the ErrorHandler.
     * @param string $logFilePath The file path to the log file.
     */
    private function __construct(SessionManager $sessionManager, ErrorHandler $errorHandler, $logFilePath)
    {
        $this->sessionManager = $sessionManager;
        $this->errorHandler = $errorHandler;
        $this->logFilePath = $logFilePath;
    }

    /**
     * Get the single instance of the Logger class.
     *
     * The getInstance method checks if an instance already exists. If not, it creates a new instance
     * and returns it. This method ensures that only one instance of the Logger class is used.
     *
     * @param SessionManager $sessionManager An instance of the SessionManager.
     * @param ErrorHandler $errorHandler An instance of the ErrorHandler.
     * @param string $logFilePath The file path to the log file.
     * @return Logger The single instance of the Logger class.
     */
    public static function getInstance(SessionManager $sessionManager, ErrorHandler $errorHandler, $logFilePath)
    {
        if (null === static::$instance) {
            static::$instance = new static($sessionManager, $errorHandler, $logFilePath);
        }

        return static::$instance;
    }

    /**
     * Log a message with a specific log level and context.
     *
     * The log method is used to log messages with different log levels. It formats the log entry,
     * opens the log file, writes the entry, and closes the file. If any error occurs during this process,
     * it is handled by the ErrorHandler.
     *
     * @param string $message The locale key of the message to be logged.
     * @param string $level The level of log (e.g., 'info', 'error').
     * @param array $context Additional context information (e.g., 'module', 'function').
     * @return bool Returns true if the log entry was successfully written, false otherwise.
     */
    public function log(string $message, string $level, array $context = []): bool
    {
        // Format the timestamp for the log entry.
        $timestamp = (new DateTime())->format('Y-m-d H:i:s');

        // Retrieve module, function, and user information from context or fallbacks.
        $module = $context['module'] ?? (defined('__CLASS__') ? __CLASS__ : basename(__FILE__));
        $function = $context['function'] ?? (defined('__FUNCTION__') ? __FUNCTION__ : 'global');
        $user = $this->sessionManager->get('user_id') ?? 'System';

        // Format the log entry.
        $logEntry = "[$timestamp] [$module] [$function] [$user] [$level] $message\n";

        // Open the log file for writing.
        $logFile = fopen($this->logFilePath, 'a');
        if (!$logFile) {
            $this->errorHandler->handleError('logger.error_open_file');
            return false;
        }

        // Write the log entry to the file.
        if (fwrite($logFile, $logEntry) === false) {
            $this->errorHandler->handleError('logger.error_write_file');
            fclose($logFile);
            return false;
        }

        // Close the log file.
        fclose($logFile);
        return true;
    }
}