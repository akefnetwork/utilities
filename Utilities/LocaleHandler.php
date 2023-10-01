<?php

namespace Utilities;

use Configuration\Configuration;
use Utilities\Contracts\LocaleHandlerInterface;
use Exception;

/**
 * LocaleHandler Utility Class
 *
 * This class is responsible for managing the application's localization features.
 * It determines the user's locale and provides translated text based on provided locale keys.
 *
 * Flow:
 * 1. Fetch default locale and translation file path from the Configuration.
 * 2. Determine the locale based on user preference, browser's Accept-Language header, or default to the provided locale.
 * 3. Load translations from the appropriate .json file based on the determined locale.
 * 4. Provide translated text based on locale keys or return the key itself if translation is not available.
 *
 * @category Utilities
 * @package  AkefNetwork
 * @author   Brahim Akef <b@akef.net>
 * @link     https://github.com/akefnetwork
 */
class LocaleHandler implements LocaleHandlerInterface
{
    /**
     * @var LocaleHandler $instance The single instance of the class.
     */
    private static $instance;

    /**
     * @var string $locale The determined locale.
     */
    private $locale;

    /**
     * @var array $translations Associative array of translations based on the determined locale.
     */
    private $translations;

    /**
     * @var string $translationFilePath Path to the translation files.
     */
    private $translationFilePath;

    /**
     * LocaleHandler constructor.
     *
     * @param string $defaultLocale The default locale.
     * @param string $translationFilePath Path to the translation files.
     */
    private function __construct(string $defaultLocale, string $translationFilePath)
    {
        $this->translationFilePath = $translationFilePath;

        try {
            $this->setLocale($defaultLocale);
            $this->loadTranslations();
            Logger::getInstance()->log('localehandler.init_success', 'info', ['locale' => $this->locale]);
        } catch (Exception $e) {
            ErrorHandler::getInstance()->handleError('localehandler.init_error', ['error_message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieves the single instance of the class.
     *
     * @return self The single instance of the class.
     */
    public static function getInstance(): self
    {
        $config = Configuration::getSettings();
        $defaultLocale = $config['localization']['default_locale'];
        $translationFilePath = $config['localization']['translations_path'] . '/';

        if (null === static::$instance) {
            static::$instance = new static($defaultLocale, $translationFilePath);
        }

        return static::$instance;
    }

    /**
     * Determines the locale.
     *
     * Priority:
     * 1. User's preference stored in session.
     * 2. Browser's Accept-Language header.
     * 3. Fallback to the provided default locale.
     *
     * @param string $defaultLocale The default locale.
     */
    public function setLocale(string $defaultLocale): void
    {
        $this->locale = SessionManager::getInstance()->getUserPreferredLocale();

        if (!$this->locale && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $this->locale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        }

        if (!$this->locale) {
            $this->locale = $defaultLocale;
        }

        Logger::getInstance()->log('localehandler.setlocale', 'info', ['locale' => $this->locale]);
    }

    /**
     * Loads translations from the appropriate .json file.
     *
     * - Constructs the file path using the determined locale.
     * - Loads translations into the $translations property.
     * - Handles errors, e.g., file not found or JSON decode errors.
     */
    public function loadTranslations(): void
    {
        $filePath = $this->translationFilePath . $this->locale . '.json';

        if (!file_exists($filePath)) {
            ErrorHandler::getInstance()->handleError('localehandler.translations_not_found', ['error_message' => "Translation file for {$this->locale} not found."]);
            return;
        }

        $this->translations = json_decode(file_get_contents($filePath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            ErrorHandler::getInstance()->handleError('localehandler.translations_load_error', ['error_message' => 'Error decoding translation file.']);
        }

        Logger::getInstance()->log('localehandler.translations_loaded', 'info', ['locale' => $this->locale]);
    }

    /**
     * Retrieves the translated text based on the provided key.
     *
     * If the translation key doesn't exist:
     * - Logs a warning.
     * - Returns the key itself.
     *
     * @param string $key The translation key.
     * @return string The translated text or the key itself if translation is not available.
     */
    public function translate(string $key): string
    {
        if (isset($this->translations[$key])) {
            return $this->translations[$key];
        }

        Logger::getInstance()->log('localehandler.translation_not_found', 'warning', ['key' => $key]);

        return $key;
    }
}
