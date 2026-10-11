<?php

namespace Kavenegar\Tests;

use Kavenegar\KavenegarApi;

class LineTests extends BaseTestCase
{
    private function getApi()
    {
        $api = new KavenegarApi(self::getApiKey());
        if (self::getApiKey() === "<put your apikey here>") {
            $this->markTestSkipped('Real API key not configured.');
        }
        return $api;
    }

    public function testBlockedLineOperations()
    {
        $api = $this->getApi();

        // 1. Add Blocked Line
        $addRes = $api->addBlockedLine(self::getReceptor(), self::getSender());
        $this->assertNotNull($addRes);

        // 2. Check Exists
        $existsRes = $api->blockedLineExists(self::getSender(), self::getReceptor());
        $this->assertNotNull($existsRes);

        // 3. List Blocked Lines
        $listRes = $api->listBlockedLines(self::getSender(), 1, time() - 86400 * 30);
        $this->assertNotNull($listRes);

        // 4. Remove Blocked Line
        $removeRes = $api->removeBlockedLine(self::getReceptor(), self::getSender());
        $this->assertNotNull($removeRes);
    }
}
