<?php

namespace Kavenegar\Tests;

use Kavenegar\KavenegarApi;

class SmsTests extends BaseTestCase
{
    private function getApi()
    {
        $api = new KavenegarApi(self::getApiKey());
        if (self::getApiKey() === "<put your apikey here>") {
            $this->markTestSkipped('Real API key not configured.');
        }
        return $api;
    }

    public function testSmsSendOverload1()
    {
        $api = $this->getApi();
        $res = $api->send(self::getSender(), [self::getReceptor()], "تست پیام ۱");
        $this->assertNotNull($res);
    }

    public function testSmsSendOverload2()
    {
        $api = $this->getApi();
        $res = $api->send(self::getSender(), self::getReceptor(), "تست پیام ۲");
        $this->assertNotNull($res);
    }

    public function testSmsSendOverload3()
    {
        $api = $this->getApi();
        $res = $api->send(self::getSender(), self::getReceptor(), "تست پیام ۳", null, 1);
        $this->assertNotNull($res);
    }

    public function testSmsSendOverload4()
    {
        $api = $this->getApi();
        $res = $api->send(self::getSender(), [self::getReceptor()], "تست پیام ۴", null, 1);
        $this->assertNotNull($res);
    }

    public function testSmsSendOverload5()
    {
        $api = $this->getApi();
        $res = $api->send(self::getSender(), self::getReceptor(), "تست پیام ۵", null, 1, "localid123");
        $this->assertNotNull($res);
    }

    public function testSmsSendOverload6()
    {
        $api = $this->getApi();
        $res = $api->send(self::getSender(), self::getReceptor(), "تست پیام ۶", null, null, "localid1234");
        $this->assertNotNull($res);
    }

    public function testSmsSendOverload7()
    {
        $api = $this->getApi();
        $res = $api->send(self::getSender(), [self::getReceptor()], "تست پیام ۷", null, null, "localid12345");
        $this->assertNotNull($res);
    }

    public function testSmsSendArrayOverload1()
    {
        $api = $this->getApi();
        $res = $api->sendArray([self::getSender()], [self::getReceptor()], ["تست پیام ۱"]);
        $this->assertNotNull($res);
    }

    public function testSmsSendArrayOverload2()
    {
        $api = $this->getApi();
        $res = $api->sendArray(self::getSender(), [self::getReceptor()], ["تست پیام ۲"], null, 1);
        $this->assertNotNull($res);
    }

    public function testSmsSendArrayOverload3()
    {
        $api = $this->getApi();
        $res = $api->sendArray(self::getSender(), [self::getReceptor()], ["تست پیام ۳"], null, 1, "localmessageid123");
        $this->assertNotNull($res);
    }

    public function testSmsSendArrayOverload4()
    {
        $api = $this->getApi();
        $res = $api->sendArray(self::getSender(), [self::getReceptor()], ["تست پیام ۴"], null, null, "localmessageid1234");
        $this->assertNotNull($res);
    }

    public function testSmsSendArrayOverload5()
    {
        $api = $this->getApi();
        $res = $api->sendArray([self::getSender()], [self::getReceptor()], ["تست پیام ۵"], null, null, "localmessageid12345");
        $this->assertNotNull($res);
    }

    public function testSmsStatusOverload1()
    {
        $api = $this->getApi();
        $res = $api->status(["123456"]);
        $this->assertNotNull($res);
    }

    public function testSmsStatusOverload2()
    {
        $api = $this->getApi();
        $res = $api->status("123456");
        $this->assertNotNull($res);
    }

    public function testSmsStatusLocalMessageIdOverload1()
    {
        $api = $this->getApi();
        $res = $api->statusLocalMessageId(["localid123"]);
        $this->assertNotNull($res);
    }

    public function testSmsStatusLocalMessageIdOverload2()
    {
        $api = $this->getApi();
        $res = $api->statusLocalMessageId("localid123");
        $this->assertNotNull($res);
    }

    public function testSmsSelectOverload1()
    {
        $api = $this->getApi();
        $res = $api->select(["123456"]);
        $this->assertNotNull($res);
    }

    public function testSmsSelectOverload2()
    {
        $api = $this->getApi();
        $res = $api->select("123456");
        $this->assertNotNull($res);
    }

    public function testSmsSelectOutboxOverload1()
    {
        $api = $this->getApi();
        $res = $api->selectOutbox(time() - 3600);
        $this->assertNotNull($res);
    }

    public function testSmsSelectOutboxOverload2()
    {
        $api = $this->getApi();
        $res = $api->selectOutbox(time() - 3600, time());
        $this->assertNotNull($res);
    }

    public function testSmsSelectOutboxOverload3()
    {
        $api = $this->getApi();
        $res = $api->selectOutbox(time() - 3600, time(), self::getSender());
        $this->assertNotNull($res);
    }

    public function testSmsLatestOutboxOverload1()
    {
        $api = $this->getApi();
        $res = $api->latestOutbox(10);
        $this->assertNotNull($res);
    }

    public function testSmsLatestOutboxOverload2()
    {
        $api = $this->getApi();
        $res = $api->latestOutbox(10, self::getSender());
        $this->assertNotNull($res);
    }

    public function testSmsCountOutboxOverload1()
    {
        $api = $this->getApi();
        $res = $api->countOutbox(time() - 3600);
        $this->assertNotNull($res);
    }

    public function testSmsCountOutboxOverload2()
    {
        $api = $this->getApi();
        $res = $api->countOutbox(time() - 3600, time());
        $this->assertNotNull($res);
    }

    public function testSmsCountOutboxOverload3()
    {
        $api = $this->getApi();
        $res = $api->countOutbox(time() - 3600, time(), 1);
        $this->assertNotNull($res);
    }

    public function testSmsCancelOverload1()
    {
        $api = $this->getApi();
        $res = $api->cancel(["123456"]);
        $this->assertNotNull($res);
    }

    public function testSmsCancelOverload2()
    {
        $api = $this->getApi();
        $res = $api->cancel("123456");
        $this->assertNotNull($res);
    }

    public function testSmsReceive()
    {
        $api = $this->getApi();
        $res = $api->receive(self::getSender(), 0);
        $this->assertNotNull($res);
    }

    public function testSmsCountInboxOverload1()
    {
        $api = $this->getApi();
        $res = $api->countInbox(time() - 3600);
        $this->assertNotNull($res);
    }

    public function testSmsCountInboxOverload2()
    {
        $api = $this->getApi();
        $res = $api->countInbox(time() - 3600, time());
        $this->assertNotNull($res);
    }

    public function testSmsCountInboxOverload3()
    {
        $api = $this->getApi();
        $res = $api->countInbox(time() - 3600, time(), self::getSender(), 0);
        $this->assertNotNull($res);
    }



    public function testSmsStatusByReceptor()
    {
        $api = $this->getApi();
        $res = $api->statusByReceptor(self::getReceptor(), time() - 3600, time());
        $this->assertNotNull($res);
    }

    public function testSmsUnreads()
    {
        $api = $this->getApi();
        $res = $api->unreads(self::getSender(), 0);
        $this->assertNotNull($res);
    }

    public function testSmsInboxPaged()
    {
        $api = $this->getApi();
        $res = $api->inboxPaged(self::getSender(), 0, time() - 3600, time(), 1);
        $this->assertNotNull($res);
    }
    public function testSmsGroupSendReport()
    {
        $api = $this->getApi();
        $res = $api->groupSendReport(time() - 3600, time());
        $this->assertNotNull($res);
    }

    public function testSmsSendUsingSendRequest()
    {
        $api = $this->getApi();
        $req = new \Kavenegar\Models\SendRequest();
        $req->sender = self::getSender();
        $req->receptor = self::getReceptor();
        $req->message = "Test using SendRequest DTO";
        $res = $api->send($req);
        $this->assertNotNull($res);
        $this->assertEquals("Test using SendRequest DTO", $res[0]->message);
    }

    public function testSmsSendArrayUsingSendArrayRequest()
    {
        $api = $this->getApi();
        $req = new \Kavenegar\Models\SendArrayRequest();
        $req->senders = array(self::getSender());
        $req->receptors = array(self::getReceptor());
        $req->messages = array("Test using SendArrayRequest DTO");
        $res = $api->sendArray($req);
        $this->assertNotNull($res);
        $this->assertEquals("Test using SendArrayRequest DTO", $res[0]->message);
    }
}
