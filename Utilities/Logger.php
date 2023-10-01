<?php

namespace Utilities;

use Utilities\Contracts\LoggerInterface;
use DateTime;

/**
 * Class Logger
 *
 * The Logger class offers a unified method to record and manage log messages across various levels of severity.
 * Employing the Singleton design pattern ensures only a single Logger instance is active, promoting efficient resource management.
 *
 * Features:
 * - Record logs in a consistent format with timestamps.
 * - Allow dynamic configuration of dependencies to maintain the Singleton pattern.
 * - Support various log levels to categorize messages accordingly.
 *
 * Dependencies:
 * - SessionManager: Retrieves user data from the session for contextual logging.
 * - ErrorHandler: Manages errors that may arise during the logging process.
 * - DateTime: Generates timestamps for each log entry.
 *
 * Usage Example:
 * Logger::configure($sessionManager, $errorHandler, 'path_to_log_file.log');
 * $logger = Logger::getInstance();
 * $logger->log('Sample message', 'info');
 *
 * @category Utilities
 * @package  AkefNetwork
 * @author   Brahim Akef <b@akef.net>
 * @link     https://github.com/akefnetwork
 */
class Logger implements LoggerInterface
{
    /**
     * @var Logger Single instance of the Logger class.
     */
    protected static $instance;

    /**
     * @var SessionManager Instance of the SessionManager to access user data.
     */
    protected static $configuredSessionManager;

    /**
     * @var ErrorHandler Instance of the ErrorHandler to manage logging errors.
     */
    protected static $configuredErrorHandler;

    /**
     * @var string Path to the log file where messages will be written.
     */
    protected static $configuredLogFilePath;

    /**
     * @var string The file path to store log messages.
     */
    protected $logFilePath;

    /**
     * @var SessionManager Instance of the SessionManager for user information retrieval.
     */
    protected $sessionManager;

    /**
     * @var ErrorHandler Instance of the ErrorHandler for logging error management.
     */
    protected $errorHandler;

    /**
     * Logger constructor.
     * Private to enforce Singleton pattern - only one instance can exist.
     *
     * @param SessionManager $sessionManager Instance of the SessionManager.
     * @param ErrorHandler $errorHandler Instance of the ErrorHandler.
     * @param string $logFilePath Path to the log file.
     */
    private function __construct(SessionManager $sessionManager, ErrorHandler $errorHandler, $logFilePath)
    {
        $this->sessionManager = $sessionManager;
        $this->errorHandler = $errorHandler;
        $this->logFilePath = $logFilePath;
    }

    /**
     * Set configuration for Logger dependencies.
     *
     * @param SessionManager $sessionManager Instance of the SessionManager.
     * @param ErrorHandler $errorHandler Instance of the ErrorHandler.
     * @param string $logFilePath Path to the log file.
     */
    public static function configure(SessionManager $sessionManager, ErrorHandler $errorHandler, $logFilePath): void
    {
        self::$configuredSessionManager = $sessionManager;
        self::$configuredErrorHandler = $errorHandler;
        self::$configuredLogFilePath = $logFilePath;
    }

    /**
     * Retrieve the single instance of Logger. If it doesn't exist, it's created.
     *
     * @return Logger The unique Logger instance.
     */
    public static function getInstance(): self
    {
        if (null === static::$instance) {
            static::$instance = new static(self::$configuredSessionManager, self::$configuredErrorHandler, self::$configuredLogFilePath);
        }
        return static::$instance;
    }

    /**
     * Set a custom Logger instance, typically for testing purposes.
     *
     * This method allows for an external Logger instance (usually a mock) to be set. This is especially useful
     * during unit testing where the real Logger instance isn't needed, and a mock or stub should be used instead.
     *
     * @param Logger|null $instance The Logger instance to set, or null to reset.
     */
    public static function setInstance(?LoggerInterface $instance): void
    {
        static::$instance = $instance;
    }

    /**
     * Record a message with a specific log level and additional context.
     *
     * @param string $message The locale key for the log message.
     * @param string $level Severity level of the log (e.g., 'info', 'error').
     * @param array $context Supplementary data for the log message.
     * @return bool True if the message is logged successfully, false otherwise.
     */
    public function log(string $message, string $level, array $context = []): bool
    {
        // Timestamp generation for the log entry.
        $timestamp = (new DateTime())->format('Y-m-d H:i:s');

        // Extract module, function, and user details from context or provide defaults.
        $module = $context['module'] ?? (defined('__CLASS__') ? __CLASS__ : basename(__FILE__));
        $function = $context['function'] ?? (defined('__FUNCTION__') ? __FUNCTION__ : 'global');
        $user = $this->sessionManager->get('user_id') ?? 'System';

        // Assemble the log entry.
        $logEntry = "[$timestamp] [$module] [$function] [$user] [$level] $message\n";

        // Open log file for appending.
        $logFile = fopen($this->logFilePath, 'a');
        if (!$logFile) {
            $this->errorHandler->handleError('logger.error_open_file');
            return false;
        }

        // Append log entry to the file.
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
