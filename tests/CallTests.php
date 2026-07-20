<?php

namespace Kavenegar\Tests;

use Kavenegar\KavenegarApi;

class CallTests extends BaseTestCase
{
    private function getApi()
    {
        $api = new KavenegarApi(self::getApiKey());
        if (self::getApiKey() === "<put your apikey here>") {
            $this->markTestSkipped('Real API key not configured.');
        }
        return $api;
    }

    public function testCallMakeTTSOverload1()
    {
        $api = $this->getApi();
        $res = $api->callMakeTTS(self::getReceptor(), "تست صوتی");
        $this->assertNotNull($res);
    }

    public function testCallMakeTTSOverload2()
    {
        $api = $this->getApi();
        $res = $api->callMakeTTS(self::getReceptor(), "تست صوتی ۲");
        $this->assertNotNull($res);
    }

    public function testCallMakeTTSOverload3()
    {
        $api = $this->getApi();
        $res = $api->callMakeTTS(self::getReceptor(), "تست صوتی ۳", null, ["localid123"], "my_tag");
        $this->assertNotNull($res);
    }
}
