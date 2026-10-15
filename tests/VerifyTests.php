<?php

namespace Kavenegar\Tests;

use Kavenegar\KavenegarApi;

class VerifyTests extends BaseTestCase
{
    private function getApi()
    {
        $api = new KavenegarApi(self::getApiKey());
        if (self::getApiKey() === "<put your apikey here>") {
            $this->markTestSkipped('Real API key not configured.');
        }
        return $api;
    }

    public function testVerifyLookup1()
    {
        $api = $this->getApi();
        $res = $api->verifyLookup(self::getReceptor(), "token123", null, null, self::getTemplate());
        $this->assertNotNull($res);
    }

    public function testVerifyLookup2()
    {
        $api = $this->getApi();
        $res = $api->verifyLookup(self::getReceptor(), "token123", null, null, self::getTemplate(), "sms");
        $this->assertNotNull($res);
    }

    public function testVerifyLookup3()
    {
        $api = $this->getApi();
        $res = $api->verifyLookup(self::getReceptor(), "token123", "token456", null, self::getTemplate());
        $this->assertNotNull($res);
    }

    public function testVerifyLookup4()
    {
        $api = $this->getApi();
        $res = $api->verifyLookup(self::getReceptor(), "token123", "token456", null, self::getTemplate(), "sms");
        $this->assertNotNull($res);
    }

    public function testVerifyLookup5()
    {
        $api = $this->getApi();
        $res = $api->verifyLookup(self::getReceptor(), "token123", "token456", "token789", self::getTemplate());
        $this->assertNotNull($res);
    }

    public function testVerifyLookup6()
    {
        $api = $this->getApi();
        $res = $api->verifyLookup(self::getReceptor(), "token123", "token456", "token789", self::getTemplate(), "sms");
        $this->assertNotNull($res);
    }

    public function testVerifyLookup7()
    {
        $api = $this->getApi();
        // receptor, token, token2, token3, template, type, token10
        $res = $api->verifyLookup(self::getReceptor(), "token1", "token2", "token3", self::getTemplate(), null, "token10");
        $this->assertNotNull($res);
    }

    public function testVerifyLookup8()
    {
        $api = $this->getApi();
        // receptor, token, token2, token3, template, type, token10
        $res = $api->verifyLookup(self::getReceptor(), "token1", "token2", "token3", self::getTemplate(), "sms", "token10");
        $this->assertNotNull($res);
    }

    public function testVerifyLookup9()
    {
        $api = $this->getApi();
        // receptor, token, token2, token3, template, type, token10, token20, tag
        $res = $api->verifyLookup(self::getReceptor(), "token1", "token2", "token3", self::getTemplate(), "sms", "token10", "token20", "my_tag");
        $this->assertNotNull($res);
    }

    public function testTemplateManagement()
    {
        $api = $this->getApi();

        // 1. List
        $templates = $api->listTemplates();
        $this->assertNotNull($templates);

        // 2. Clone (mock values)
        $index = rand(0,$this->count($templates));
        $clone = $api->cloneTemplate($templates[$index]->id, $templates[$index]->name, "new_template_" . rand(1000, 9999));
        $this->assertNotNull($clone);


        // 4. Add using TemplateDto
        $dto = new \Kavenegar\Models\TemplateDto();
        $dto->sourcetype = \Kavenegar\Enums\VerificationUsageType::WebApplication;
        $dto->sendmethod = \Kavenegar\Enums\VerificationType::Message;
        $dto->name = "temp" . rand(100000, 999999) . time();
        $dto->textmessage = "Hello %token% DTO";

        $newTemplateDto = $api->addTemplate($dto);
        $this->assertNotNull($newTemplateDto);

        $remove = $api->removeTemplate($newTemplateDto->templateid);
        $this->assertNotNull($newTemplateDto);
    }
}
