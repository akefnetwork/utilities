<?php

namespace Utilities\Contracts;

/**
 * LocaleHandlerInterface
 * 
 * Defines the contract that any LocaleHandler must adhere to.
 * This interface outlines the methods responsible for managing localization
 * in the application, including setting the locale, loading translations,
 * and translating locale keys.
 *
 * @category Contracts
 * @package  AkefNetwork
 * @author   Brahim Akef <b@akef.net>
 * @link     https://github.com/akefnetwork
 */
interface LocaleHandlerInterface
{
    /**
     * Set the locale for the application.
     *
     * @return void
     */
    public function setLocale(): void;

    /**
     * Load translations for the current locale.
     *
     * @return void
     */
    public function loadTranslations(): void;

    /**
     * Translate the given locale key to the localized text.
     *
     * @param string $key The locale key to be translated.
     * @return string The localized text.
     */
    public function translate(string $key): string;
}
