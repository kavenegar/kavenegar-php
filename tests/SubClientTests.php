<?php

namespace Kavenegar\Tests;

use Kavenegar\KavenegarApi;

class SubClientTests extends BaseTestCase
{
    private function getApi()
    {
        $api = new KavenegarApi(self::getApiKey());
        if (self::getApiKey() === "<put your apikey here>") {
            $this->markTestSkipped('Real API key not configured.');
        }
        return $api;
    }

    public function testSubClientOperations()
    {
        $api = $this->getApi();

        // 1. Add Client
        $params = array(
            "username" => "sub_" . time(),
            "password" => "password123",
            "fullname" => "Sub Client",
            "mobile" => "0912" . rand(1000000, 9999999),
            "status" => 1,
            "credit" => 1000
        );
        $client = $api->addClient($params);
        $this->assertNotNull($client);

        // 2. List Clients
        $clients = $api->listClients();
        $this->assertNotNull($clients);

        // 3. Fetch Client
        $client = $api->fetchClient("mock_apikey");
        $this->assertNotNull($client);

        // 4. Fetch Client by Local ID
        $client = $api->fetchClientByLocalId("local_123");
        $this->assertNotNull($client);

        // 5. Renew key
        $client = $api->renewClientKey("mock_apikey");
        $this->assertNotNull($client);

        // 6. Set Status
        $client = $api->setClientStatus("mock_apikey", 2);
        $this->assertNotNull($client);

        // 7. Charge Credit
        $client = $api->chargeClientCredit("mock_apikey", 500);
        $this->assertNotNull($client);
    }
}
