<?php

namespace Tests;

use Configuration\Configuration;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigurationTest
 *
 * This test class focuses on verifying the proper functioning of the Configuration utility.
 * It ensures that configuration values are loaded and retrieved correctly.
 *
 * @category Tests
 * @package  AkefNetwork
 * @author   Brahim Akef <b@akef.net>
 * @link     https://github.com/akefnetwork
 */
class ConfigurationTest extends TestCase
{
    /**
     * Test configuration value retrieval.
     *
     * This test ensures:
     * - Configuration values are correctly retrieved.
     */
    public function testGetConfigurationValue()
    {
        $logFilePath = Configuration::get('logFilePath');
        $this->assertNotNull($logFilePath, 'logFilePath should not be null.');
        $this->assertIsString($logFilePath, 'logFilePath should be a string.');
    }
}

