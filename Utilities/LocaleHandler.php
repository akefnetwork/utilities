<?php

namespace Utilities;

use Configuration\Configuration;

/**
 * LocaleHandler class
 * 
 * The LocaleHandler class is responsible for managing the application's locale settings and translations.
 * It provides functionalities to set and get the current locale, load translations, and fetch translations for specific keys.
 * 
 * Usage:
 * 1. Obtain an instance using the `getInstance()` method.
 * 2. Set or get the locale using the `setLocale()` and `getLocale()` methods.
 * 3. Fetch translations using the `translate()` method.
 * 
 * Note:
 * This class also logs specific events and handles errors related to translations.
 *
 * @category Utilities
 * @package  LocaleHandler
 * @author   Brahim Akef <b@akef.net>
 * @link     http://github.com/akefnetwork
 */
class LocaleHandler implements LocaleHandlerInterface
{
    /**
     * @var LocaleHandler|null The singleton instance of LocaleHandler.
     */
    private static ?LocaleHandler $instance = null;

    /**
     * @var string The currently set locale.
     */
    private string $locale;

    /**
     * @var string The default locale to fallback to.
     */
    private string $defaultLocale;

    /**
     * @var array The loaded translations.
     */
    private array $translations = [];

    /**
     * @var Logger The logger instance to log events.
     */
    private Logger $logger;

    /**
     * @var ErrorHandler The error handler instance to handle translation errors.
     */
    private ErrorHandler $errorHandler;

    /**
     * @var SessionManager The session manager instance to fetch and store the locale in session.
     */
    private SessionManager $sessionManager;

    /**
     * Constructor is private to ensure singleton behavior.
     */
    private function __construct()
    {
        $this->logger = Logger::getInstance();
        $this->errorHandler = ErrorHandler::getInstance();
        $this->sessionManager = SessionManager::getInstance();
        $this->defaultLocale = Configuration::get('localization.default_locale', 'en_US');
    }

    /**
     * Provides the singleton instance of LocaleHandler.
     * 
     * @return LocaleHandler The singleton instance.
     */
    public static function getInstance(): LocaleHandler
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Sets the application locale.
     * 
     * This method sets the locale for the application. If no locale is provided, it tries to fetch the locale from the SessionManager.
     * After setting the locale, it logs the event.
     *
     * @param string|null $locale The locale to set. If null, fetches from SessionManager.
     * @return void
     */
    public function setLocale(?string $locale = null): void
    {
        if (!$locale) {
            $locale = $this->sessionManager->get('locale', $this->defaultLocale);
        }
        $this->locale = $locale;
        $this->logger->log('localehandler.setlocale', 'info', ['locale' => $this->locale]);
    }

    /**
     * Gets the current application locale.
     * 
     * @return string The current locale.
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Loads translations from the specified translation file.
     * 
     * @param string $translationFilePath The path to the translation file.
     * @return void
     */
    private function loadTranslations(string $translationFilePath): void
    {
        if (!file_exists($translationFilePath)) {
            $this->errorHandler->handleError('localehandler.translations_not_found');
            return;
        }

        $translations = json_decode(file_get_contents($translationFilePath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->errorHandler->handleError('localehandler.translations_load_error');
            return;
        }

        $this->translations = $translations;
    }

    /**
     * Translates a given key using the loaded translations.
     * 
     * If the key is not found in the translations, it logs a warning and returns the key itself.
     *
     * @param string $key The key to translate.
     * @return string The translated string or the key itself if not found.
     */
    public function translate(string $key): string
    {
        if (!isset($this->translations[$key])) {
            $this->logger->log('localehandler.translation_not_found', 'warning', ['key' => $key]);
            return $key;
        }
        return $this->translations[$key];
    }
}
