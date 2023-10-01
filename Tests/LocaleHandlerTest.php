<?php

namespace Utilities\Tests;

use Utilities\LocaleHandler;
use Utilities\Logger;
use Utilities\ErrorHandler;
use Utilities\SessionManager;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

/**
 * LocaleHandlerTest Class
 *
 * This class is responsible for testing the functionalities offered by the LocaleHandler utility.
 * It verifies the singleton behavior, translation loading mechanisms, error handling, and more.
 *
 * @category Utilities
 * @package  Tests
 * @author   Brahim Akef <b@akef.net>
 * @link     https://github.com/akefnetwork
 */
class LocaleHandlerTest extends TestCase
{
    private $defaultLocale = 'en_US';
    private $translationFilePath;
    private $root;
    private $loggerMock;
    private $errorHandlerMock;

    /**
     * Setup method that runs before every test.
     * 
     * Initializes the virtual file system, creates a sample translation file,
     * and mocks the Logger and ErrorHandler instances.
     *
     * @return void
     */
    protected function setUp(): void
    {
        // Suppress any potential output.
        ob_start();

        // Setup a virtual file system.
        $this->root = vfsStream::setup('testing');
        $this->translationFilePath = vfsStream::url('testing/') . $this->defaultLocale . '.json';

        // Create a sample translation file.
        file_put_contents($this->translationFilePath, json_encode(['testKey' => 'Test Translation']));

        // Mock Logger.
        $this->loggerMock = $this->createMock(Logger::class);
        Logger::setInstance($this->loggerMock);

        // Mock ErrorHandler.
        $this->errorHandlerMock = $this->createMock(ErrorHandler::class);
        ErrorHandler::setInstance($this->errorHandlerMock);
    }

    /**
     * Tear down method that runs after every test.
     * 
     * Clears the output buffer.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        ob_end_clean(); // End output buffering.
    }

    /**
     * Tests whether the LocaleHandler utility returns a singleton instance.
     *
     * @return void
     */
    public function testGetInstanceCreatesOnlyOneInstance()
    {
        $instance1 = LocaleHandler::getInstance($this->defaultLocale, $this->translationFilePath);
        $instance2 = LocaleHandler::getInstance($this->defaultLocale, $this->translationFilePath);
        
        $this->assertSame($instance1, $instance2, 'LocaleHandler should return the same instance');
    }
    
    /**
     * Tests if the locale is correctly set from the SessionManager.
     *
     * @return void
     */
    public function testLocaleIsSetFromSessionManager()
    {
        $instance = LocaleHandler::getInstance($this->defaultLocale, $this->translationFilePath);
        
        // Check if Logger was called with the expected locale.
        $this->loggerMock->expects($this->once())
            ->method('log')
            ->with('localehandler.setlocale', 'info', ['locale' => 'fr_FR']);
    }
    
    /**
     * Tests if translations are loaded correctly from the translation file.
     *
     * @return void
     */
    public function testTranslationsAreLoaded()
    {
        $instance = LocaleHandler::getInstance($this->defaultLocale, $this->translationFilePath);
        $translation = $instance->translate('testKey');
        
        $this->assertEquals('Test Translation', $translation, 'Translations should be loaded correctly');
    }
    
    /**
     * Tests the behavior when a translation key is missing.
     * The method should return the key itself.
     *
     * @return void
     */
    public function testMissingTranslationReturnsKey()
    {
        $instance = LocaleHandler::getInstance($this->defaultLocale, $this->translationFilePath);
        $translation = $instance->translate('missingKey');
        
        $this->assertEquals('missingKey', $translation, 'Missing translation should return the key');
        
        // Check if Logger was called with a warning for the missing key.
        $this->loggerMock->expects($this->once())
            ->method('log')
            ->with('localehandler.translation_not_found', 'warning', ['key' => 'missingKey']);
    }
    
    /**
     * Tests the error handling mechanism when the translation file is missing.
     *
     * @return void
     */
    public function testErrorHandlingOnMissingTranslationFile()
    {
        $instance = LocaleHandler::getInstance($this->defaultLocale, vfsStream::url('testing/nonexistent.json'));
        
        // Check if ErrorHandler was called for a missing translation file.
        $this->errorHandlerMock->expects($this->once())
            ->method('handleError')
            ->with('localehandler.translations_not_found');
    }
    
    /**
     * Tests the error handling mechanism when the translation file is invalid.
     *
     * @return void
     */
    public function testErrorHandlingOnInvalidTranslationFile()
    {
        file_put_contents($this->translationFilePath, 'invalid_json');
        
        $instance = LocaleHandler::getInstance($this->defaultLocale, $this->translationFilePath);
        
        // Check if ErrorHandler was called for an invalid translation file.
        $this->errorHandlerMock->expects($this->once())
            ->method('handleError')
            ->with('localehandler.translations_load_error');
    }
    
    // Additional tests can be added here as needed.
}
