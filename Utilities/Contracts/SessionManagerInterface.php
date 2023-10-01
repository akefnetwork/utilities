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
    public function startSession(): void;
    public function set(string $key, $value): void;
    public function get(string $key);
    public function destroySession(): void;
    public function setFlash(string $key, $message): void;
    public function getFlash(string $key);
    public function setUserAuthData($userData): void;
    public function getUserAuthData();
}
