<?php

namespace Tests;

use Utilities\Logger;
use Utilities\SessionManager;
use Configuration\Configuration;
use Utilities\ErrorHandler;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Class LoggerTest
 *
 * Unit tests for the Logger utility.
 *
 * This test class focuses on verifying the proper functioning of the Logger utility.
 * It ensures that log messages are correctly formatted and written to the desired location.
 * Additionally, it checks how the Logger interacts with its dependencies, such as SessionManager.
 *
 * Features tested:
 * - Singleton pattern enforcement
 * - Log message recording with various severities and contexts
 * - File and directory creation during logging
 * - Error handling during the logging process
 *
 * @category Tests
 * @package  AkefNetwork
 * @author   Brahim Akef <b@akef.net>
 * @link     https://github.com/akefnetwork
 */
class LoggerTest extends TestCase
{
    /**
     * Path to the log file.
     *
     * @var string
     */
    private $logFilePath;

    /**
     * Setup operations to run before each test.
     *
     * - Set log file path from configuration
     * - Remove any existing log file to ensure a clean testing environment
     * - Mock the SessionManager for controlled testing
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Set log file path from configuration
        $this->logFilePath = Configuration::get('logFilePath');
        
        // Remove log file to ensure a clean environment for each test
        if (file_exists($this->logFilePath)) {
            unlink($this->logFilePath);
        }

        // Mock the SessionManager
        $mockedSessionManager = $this->createMock(SessionManager::class);
        $mockedSessionManager->method('startSession');

        Logger::setSessionManager($mockedSessionManager);
    }

    /**
     * Cleanup operations to run after each test.
     * 
     * Resetting the Logger instance for isolation between tests.
     */
    public function tearDown(): void
    {
        // Resetting the Logger instance after each test
        Logger::setInstance(null);
    }

    /**
     * Test the logging capability of the Logger.
     *
     * This test ensures:
     * - The log file is created if it doesn't exist
     * - Log entries are written correctly with the given message
     * - The Logger returns true upon successful logging
     */
    public function testLogMessage()
    {
        $mockedSessionManager = $this->createMock(SessionManager::class);
        $mockedSessionManager->method('startSession');
    
        $mockedErrorHandler = $this->createMock(ErrorHandler::class);
    
        $logger = Logger::getInstance();
        Logger::configure($mockedSessionManager, $mockedErrorHandler);
    
        $result = $logger->log('Test message', 'info');

        // Check if file exists
        $this->assertFileExists($this->logFilePath, 'Log file was not created.');

        // Check file content
        $logContent = file_get_contents($this->logFilePath);
        $this->assertStringContainsString('Test message', $logContent, 'Log message was not written to the file.');

        $this->assertTrue($result, 'Logger should successfully log messages.');
    }

    // Additional tests for various scenarios can be added below...
}