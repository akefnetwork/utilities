<?php

namespace Utilities\Tests;

use Utilities\LocaleHandler;
use Utilities\Logger;
use Utilities\ErrorHandler;
use Utilities\SessionManager;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Test class for LocaleHandler Utility
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
    
    protected function setUp(): void
    {
        // Setup a virtual file system.
        $this->root = vfsStream::setup('testing');
        $this->translationFilePath = vfsStream::url('testing/') . $this->defaultLocale . '.json';
        
        // Create a sample translation file.
        file_put_contents($this->translationFilePath, json_encode(['testKey' => 'Test Translation']));
    }
    
    public function testGetInstanceCreatesOnlyOneInstance()
    {
        $instance1 = LocaleHandler::getInstance($this->defaultLocale, $this->translationFilePath);
        $instance2 = LocaleHandler::getInstance($this->defaultLocale, $this->translationFilePath);
        
        $this->assertSame($instance1, $instance2, 'LocaleHandler should return the same instance');
    }
    
    public function testLocaleIsSetFromSessionManager()
    {
        // Assuming SessionManager::getUserPreferredLocale() returns 'fr_FR'.
        $instance = LocaleHandler::getInstance($this->defaultLocale, $this->translationFilePath);
        
        // Check if Logger was called with the expected locale.
        Logger::getInstance()->expects($this->once())
            ->method('log')
            ->with('localehandler.setlocale', 'info', ['locale' => 'fr_FR']);
    }
    
    public function testTranslationsAreLoaded()
    {
        $instance = LocaleHandler::getInstance($this->defaultLocale, $this->translationFilePath);
        $translation = $instance->translate('testKey');
        
        $this->assertEquals('Test Translation', $translation, 'Translations should be loaded correctly');
    }
    
    public function testMissingTranslationReturnsKey()
    {
        $instance = LocaleHandler::getInstance($this->defaultLocale, $this->translationFilePath);
        $translation = $instance->translate('missingKey');
        
        $this->assertEquals('missingKey', $translation, 'Missing translation should return the key');
        
        // Check if Logger was called with a warning for the missing key.
        Logger::getInstance()->expects($this->once())
            ->method('log')
            ->with('localehandler.translation_not_found', 'warning', ['key' => 'missingKey']);
    }
    
    public function testErrorHandlingOnMissingTranslationFile()
    {
        $instance = LocaleHandler::getInstance($this->defaultLocale, vfsStream::url('testing/nonexistent.json'));
        
        // Check if ErrorHandler was called for a missing translation file.
        ErrorHandler::getInstance()->expects($this->once())
            ->method('handleError')
            ->with('localehandler.translations_not_found');
    }
    
    public function testErrorHandlingOnInvalidTranslationFile()
    {
        file_put_contents($this->translationFilePath, 'invalid_json');
        
        $instance = LocaleHandler::getInstance($this->defaultLocale, $this->translationFilePath);
        
        // Check if ErrorHandler was called for an invalid translation file.
        ErrorHandler::getInstance()->expects($this->once())
            ->method('handleError')
            ->with('localehandler.translations_load_error');
    }
    
    // ... Add other tests as needed ...
}
