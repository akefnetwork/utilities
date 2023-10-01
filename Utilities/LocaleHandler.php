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
            Logger::getInstance()->log('LocaleHandler initialized successfully', 'info', ['locale' => $this->locale]);
        } catch (Exception $e) {
            ErrorHandler::getInstance()->handleError('Failed to initialize LocaleHandler', ['error_message' => $e->getMessage()]);
        }
    }

    public static function getInstance(string $defaultLocale, string $translationFilePath): self
    {
        if (null === static::$instance) {
            static::$instance = new static($defaultLocale, $translationFilePath);
        }

        return static::$instance;
    }

    public function setLocale(string $defaultLocale): void
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

        Logger::getInstance()->log('Locale set successfully', 'info', ['locale' => $this->locale]);
    }

    public function loadTranslations(): void
    {
        $filePath = $this->translationFilePath . '/' . $this->locale . '.json';

        if (!file_exists($filePath)) {
            ErrorHandler::getInstance()->handleError('Translation file not found', ['locale' => $this->locale]);
            return;
        }

        $this->translations = json_decode(file_get_contents($filePath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            ErrorHandler::getInstance()->handleError('Error decoding translation file', ['locale' => $this->locale]);
            return;
        }

        Logger::getInstance()->log('Translations loaded successfully', 'info', ['locale' => $this->locale]);
    }

    public function translate(string $key): string
    {
        if (isset($this->translations[$key])) {
            return $this->translations[$key];
        }

        Logger::getInstance()->log('Translation key not found', 'warning', ['key' => $key]);

        // Fallback to key if translation doesn't exist.
        return $key;
    }
}