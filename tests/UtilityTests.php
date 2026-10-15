<?php

namespace Kavenegar\Tests;

use Kavenegar\KavenegarApi;

class UtilityTests extends BaseTestCase
{
    public function testUtils()
    {
        $api = new KavenegarApi(self::getApiKey());
        if (self::getApiKey() === "<put your apikey here>") {
            $this->markTestSkipped('Real API key not configured.');
        }

        // Ping
        $status = $api->ping();
        $this->assertEquals("pong", $status);

        // Get Date
        $serverDate = $api->getServerDate();
        $this->assertNotNull($serverDate);
        $this->assertTrue(property_exists($serverDate, 'datetime'));
    }

    public function testAccountInfoAndConfig()
    {
        $api = new KavenegarApi(self::getApiKey());
        if (self::getApiKey() === "<put your apikey here>") {
            $this->markTestSkipped('Real API key not configured.');
        }

        // AccountInfo
        $accountInfo = $api->accountInfo();
        $this->assertNotNull($accountInfo);

        // AccountConfig
        $accountConfig = $api->accountConfig("enabled", "enabled", "enabled", self::getSender(), 10000, "enabled");
        $this->assertNotNull($accountConfig);
    }
}
