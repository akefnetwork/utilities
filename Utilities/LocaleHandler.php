<?php

namespace Utilities;

use Utilities\Contracts\LocaleHandlerInterface;
use Exception;

/**
 * LocaleHandler Utility
 *
 * Manages localization in the application by determining the user's locale
 * and providing localized text based on locale keys.
 *
 * @category Utilities
 * @package  AkefNetwork
 * @author   Brahim Akef <b@akef.net>
 * @link     https://github.com/akefnetwork
 */
class LocaleHandler implements LocaleHandlerInterface
{
    private static $instance;
    private $locale;
    private $translations;
    private $translationFilePath;

    private function __construct(string $defaultLocale, string $translationFilePath)
    {
        $this->translationFilePath = $translationFilePath;

        try {
            $this->setLocale($defaultLocale);
            $this->loadTranslations();
            Logger::getInstance()->log('localehandler.init_success', 'info', ['locale' => $this->locale]);
        } catch (Exception $e) {
            ErrorHandler::getInstance()->handleError('localehandler.init_error', $e->getMessage());
        }
    }

    public static function getInstance(string $defaultLocale, string $translationFilePath): self
    {
        if (null === static::$instance) {
            static::$instance = new static($defaultLocale, $translationFilePath);
        }

        return static::$instance;
    }

    private function setLocale(string $defaultLocale): void
    {
        // 1. Check for a user's preference stored in session.
        $this->locale = SessionManager::getInstance()->getUserPreferredLocale();

        // 2. If not set, check the browser's Accept-Language header.
        if (!$this->locale && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $this->locale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        }

        // 3. Fallback to the provided default locale if necessary.
        if (!$this->locale) {
            $this->locale = $defaultLocale;
        }

        Logger::getInstance()->log('localehandler.setlocale', 'info', ['locale' => $this->locale]);
    }

    private function loadTranslations(): void
    {
        $filePath = $this->translationFilePath . $this->locale . '.json';

        if (!file_exists($filePath)) {
            ErrorHandler::getInstance()->handleError('localehandler.translations_not_found', "Translation file for {$this->locale} not found.");
            return;
        }

        $this->translations = json_decode(file_get_contents($filePath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            ErrorHandler::getInstance()->handleError('localehandler.translations_load_error', 'Error decoding translation file.');
        }

        Logger::getInstance()->log('localehandler.translations_loaded', 'info', ['locale' => $this->locale]);
    }

    public function translate(string $key): string
    {
        if (isset($this->translations[$key])) {
            return $this->translations[$key];
        }

        Logger::getInstance()->log('localehandler.translation_not_found', 'warning', ['key' => $key]);

        // Fallback to key if translation doesn't exist.
        return $key;
    }
}
