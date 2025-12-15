<?php

namespace Kavenegar\Tests;

use PHPUnit\Framework\TestCase;
use Kavenegar\Exceptions\ApiException;
use Kavenegar\Exceptions\HttpException;
use Kavenegar\Exceptions\NotProperlyConfiguredException;

class ExceptionsTest extends TestCase
{
    public function testApiExceptionName(): void
    {
        $exception = new ApiException("Test message", 400);
        $this->assertEquals('ApiException', $exception->getName());
    }

    public function testHttpExceptionName(): void
    {
        $exception = new HttpException("Test message", 500);
        $this->assertEquals('HttpException', $exception->getName());
    }

    public function testNotProperlyConfiguredExceptionName(): void
    {
        $exception = new NotProperlyConfiguredException("Test message", 0);
        $this->assertEquals('NotProperlyConfigured', $exception->getName());
    }

    public function testApiExceptionErrorMessage(): void
    {
        $exception = new ApiException("Test error", 400);
        $errorMessage = $exception->errorMessage();
        $this->assertStringContainsString('ApiException', $errorMessage);
        $this->assertStringContainsString('400', $errorMessage);
        $this->assertStringContainsString('Test error', $errorMessage);
    }

    public function testHttpExceptionErrorMessage(): void
    {
        $exception = new HttpException("Connection failed", 500);
        $errorMessage = $exception->errorMessage();
        $this->assertStringContainsString('HttpException', $errorMessage);
        $this->assertStringContainsString('500', $errorMessage);
        $this->assertStringContainsString('Connection failed', $errorMessage);
    }

    public function testExceptionConstructorAcceptsStringMessage(): void
    {
        $exception = new ApiException("Test message", 400);
        $this->assertEquals("Test message", $exception->getMessage());
        $this->assertEquals(400, $exception->getCode());
    }

    public function testExceptionConstructorDefaultCode(): void
    {
        $exception = new ApiException("Test message");
        $this->assertEquals(0, $exception->getCode());
    }
}
