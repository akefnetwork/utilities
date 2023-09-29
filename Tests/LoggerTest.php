<?php

namespace Tests;

use Utilities\Logger;
use Utilities\ErrorHandler;
use Utilities\SessionManager;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class LoggerTest extends TestCase
{
    private $logFilePath;
    private $root;
    
    protected function setUp(): void
    {
        // Setup a virtual file system.
        $this->root = vfsStream::setup('testing');
        $this->logFilePath = vfsStream::url('testing/log.txt');
        
        // Instantiate Logger with dependencies.
        $this->logger = Logger::getInstance(
            new SessionManager(),
            new ErrorHandler(),
            $this->logFilePath
        );
    }
    
    public function testLogMessage()
    {
        $sessionManager = SessionManager::getInstance();
        // Define a sample log message.
        $message = 'INFO_MESSAGE';
        $level = 'info';
        $context = ['module' => 'UserModule', 'function' => 'signIn'];
        
        // Log the message.
        $result = $this->logger->log($message, $level, $context);
        
        // Assert that the log entry was successfully written.
        $this->assertTrue($result);
        
        // Read the log file and assert the log entry is correctly formatted.
        $logEntry = file_get_contents($this->logFilePath);
        $this->assertStringContainsString($message, $logEntry);
        $this->assertStringContainsString($level, $logEntry);
        $this->assertStringContainsString($context['module'], $logEntry);
        $this->assertStringContainsString($context['function'], $logEntry);
    }
    
    // Additional tests like testErrorHandling, testFileWriting etc. can be added here.
}
