<?php

namespace Kavenegar\Tests;

use Kavenegar\KavenegarApi;

class MediaTests extends BaseTestCase
{
    private function getApi()
    {
        $api = new KavenegarApi(self::getApiKey());
        if (self::getApiKey() === "<put your apikey here>") {
            $this->markTestSkipped('Real API key not configured.');
        }
        return $api;
    }

    public function testMediaOperations()
    {
        $api = $this->getApi();

        // 1. List Media
        $mediaList = $api->listMedia(1, 10);
        $this->assertNotNull($mediaList);

        // 2. Download a real test image and upload it
        $imageUrl = "https://fastly.picsum.photos/id/237/200/200.jpg?hmac=zHUGikXUDyLCCmvyww1izLK3R3k8oRYBRiTizZEdyfI";
        $bytes = @file_get_contents($imageUrl);
        if ($bytes === false) {
            $this->markTestSkipped("Failed to download test image from Picsum.");
        }

        // Upload media bytes
        $uploaded = $api->uploadMediaData("test_image.jpg", $bytes);
        $this->assertNotNull($uploaded);
        $this->assertNotNull($uploaded->id);

        $error = null;
        try {
            // 3. Get Media details
            $details = $api->getMedia($uploaded->id);
            $this->assertNotNull($details);
        } catch (\Exception $e) {
            $error = $e;
        }

        // 4. Clean up uploaded media
        $deleteRes = $api->deleteMedia($uploaded->id);
        $this->assertNotNull($deleteRes);

        if ($error) {
            throw $error;
        }
    }
}
