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

    public function testVerifyLookupOverload1()
    {
        $api = $this->getApi();
        $res = $api->verifyLookup(self::getReceptor(), "token123", self::getTemplate());
        $this->assertNotNull($res);
    }

    public function testVerifyLookupOverload2()
    {
        $api = $this->getApi();
        $res = $api->verifyLookup(self::getReceptor(), "token123", self::getTemplate(), "sms");
        $this->assertNotNull($res);
    }

    public function testVerifyLookupOverload3()
    {
        $api = $this->getApi();
        $res = $api->verifyLookup(self::getReceptor(), "token123", "token456", self::getTemplate());
        $this->assertNotNull($res);
    }

    public function testVerifyLookupOverload4()
    {
        $api = $this->getApi();
        $res = $api->verifyLookup(self::getReceptor(), "token123", "token456", self::getTemplate(), "sms");
        $this->assertNotNull($res);
    }

    public function testVerifyLookupOverload5()
    {
        $api = $this->getApi();
        $res = $api->verifyLookup(self::getReceptor(), "token123", "token456", "token789", self::getTemplate());
        $this->assertNotNull($res);
    }

    public function testVerifyLookupOverload6()
    {
        $api = $this->getApi();
        $res = $api->verifyLookup(self::getReceptor(), "token123", "token456", "token789", self::getTemplate(), "sms");
        $this->assertNotNull($res);
    }

    public function testVerifyLookupOverload7()
    {
        $api = $this->getApi();
        // receptor, token, token2, token3, token10, template
        $res = $api->verifyLookup(self::getReceptor(), "token1", "token2", "token3", "token10", self::getTemplate());
        $this->assertNotNull($res);
    }

    public function testVerifyLookupOverload8()
    {
        $api = $this->getApi();
        // receptor, token, token2, token3, token10, template, type
        $res = $api->verifyLookup(self::getReceptor(), "token1", "token2", "token3", "token10", self::getTemplate(), "sms");
        $this->assertNotNull($res);
    }

    public function testVerifyLookupOverload9()
    {
        $api = $this->getApi();
        // receptor, token, token2, token3, token10, token20, template, type, tag
        $res = $api->verifyLookup(self::getReceptor(), "token1", "token2", "token3", "token10", "token20", self::getTemplate(), "sms", "my_tag");
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
