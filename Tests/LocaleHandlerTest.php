<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use Utilities\LocaleHandler;
use Utilities\Logger;
use Utilities\ErrorHandler;

/**
 * LocaleHandlerTest
 *
 * Tests for the LocaleHandler utility class.
 *
 * @category Utilities
 * @package  AkefNetwork
 * @author   Brahim Akef <b@akef.net>
 * @link     https://github.com/akefnetwork
 */
class LocaleHandlerTest extends TestCase
{
    private $root;
    private $translationFilePath;
    private $localeHandler;

    protected function setUp(): void
    {
        // Setup a virtual file system.
        $this->root = vfsStream::setup('testing');
        $this->translationFilePath = vfsStream::url('testing/translations.json');

        // Create a mock translation file in the virtual file system.
        vfsStream::newFile('translations.json')
            ->withContent('{"test_key": "Test Translation"}')
            ->at($this->root);

        // Instantiate LocaleHandler with dependencies.
        $this->localeHandler = LocaleHandler::getInstance('en_US', $this->translationFilePath);
    }

    public function testTranslationKeyExists(): void
    {
        $translatedText = $this->localeHandler->translate('test_key');
        $this->assertEquals('Test Translation', $translatedText);
    }

    // TODO: Add more tests like testing the instantiation, testing for non-existent keys, etc.
}
