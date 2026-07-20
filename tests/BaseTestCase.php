<?php

namespace Kavenegar\Tests;

use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    public static function getApiKey()
    {
        return getenv('KAVENEGAR_API_KEY') ?: "<put your apikey here>";
    }

    public static function getReceptor()
    {
        return getenv('KAVENEGAR_RECEPTOR') ?: "<put your receptor here>";
    }

    public static function getSender()
    {
        return getenv('KAVENEGAR_SENDER') ?: "<put your sender here>" ;
    }

    public static function getTemplate()
    {
        return getenv('KAVENEGAR_TEMPLATE') ?: "<put your template here>";
    }
}
