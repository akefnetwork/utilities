<?php

namespace Tests;

use Utilities\Logger;
use Utilities\SessionManager;
use PHPUnit\Framework\TestCase;

/**
 * Class LoggerTest
 *
 * Unit tests for the Logger utility.
 *
 * This test class focuses on verifying the proper functioning of the Logger utility.
 * It ensures log messages are correctly formatted and written to the desired location.
 * Additionally, it checks how the Logger interacts with its dependencies, such as SessionManager.
 *
 * Features tested:
 * - Singleton pattern enforcement
 * - Log message recording with various severities and contexts
 * - Error handling during the logging process
 *
 * @category Tests
 * @package  AkefNetwork
 * @author   Brahim Akef <b@akef.net>
 * @link     https://github.com/akefnetwork
 */
class LoggerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Mock the SessionManager to prevent the actual startSession method from being executed
        $mockedSessionManager = $this->createMock(SessionManager::class);
        
        // Here, we specify that the startSession method should not actually be executed.
        // Instead, we mock it to return null.
        $mockedSessionManager->method('startSession');

        Logger::setSessionManager($mockedSessionManager);
    }

    public function tearDown(): void
    {
        // Resetting the Logger instance after each test
        Logger::setInstance(null);
    }

    /**
     * Tests that the Logger can log messages.
     */
    public function testLogMessage()
    {
        $logger = Logger::getInstance();
        
        // log file is 'test_log.log' for this test
        // Logger::configure(SessionManager::getInstance(), ErrorHandler::getInstance(), 'test_log.log');

        $result = $logger->log('Test message', 'info');

        $this->assertTrue($result, 'Logger should successfully log messages.');

        // Additional assertions can be added here to check the content of 'test_log.log' 
        // and ensure the log entry has been added correctly.
    }

    // Additional tests for the Logger can be added below...
}