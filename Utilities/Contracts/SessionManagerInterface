<?php

namespace Utilities\Contracts;

/**
 * Interface SessionManagerInterface
 *
 * Defines the contract for the SessionManager class. Any class implementing
 * this interface must provide implementations for session management methods,
 * ensuring consistent session handling across different SessionManager implementations.
 *
 * @category Utilities
 * @package  AkefNetwork
 * @author   Brahim Akef <b@akef.net>
 * @link     https://github.com/akefnetwork
 */
interface SessionManagerInterface
{
    public static function startSession(): void;
    public static function set(string $key, $value): void;
    public static function get(string $key);
    public static function destroySession(): void;
    public static function setFlash(string $key, $message): void;
    public static function getFlash(string $key);
    public static function setUserAuthData($userData): void;
    public static function getUserAuthData();
}
