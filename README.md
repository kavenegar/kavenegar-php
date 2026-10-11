<div align="center">

<br/>

# 📱 Kavenegar PHP SDK

**A modern, lightweight PHP client for SMS, OTP & Voice — fully typed and compliant down to PHP 5.4.**

<br/>

[![Packagist Version](https://img.shields.io/packagist/v/kavenegar/php?style=for-the-badge&color=8892BF&logo=composer&label=Packagist)](https://packagist.org/packages/kavenegar/php)
[![Downloads](https://img.shields.io/packagist/dt/kavenegar/php?style=for-the-badge&color=00b4d8&logo=composer&label=Downloads)](https://packagist.org/packages/kavenegar/php)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%205.4.0-777BB4?style=for-the-badge&logo=php)](https://php.net)

<br/>

[📦 Install](#-installation) · [🚀 Quick Start](#-quick-start) · [📝 DTO Requests](#-dto-request-objects) · [📖 Docs](http://kavenegar.com/rest.html) · [🇮🇷 فارسی](#-راهنما)

<br/>

</div>

---

## ✨ Features

* 📦 **Strict DTO Mapping:** Method calls explicitly return fully structured model objects (e.g., `SendResult`, `StatusResult`) instead of raw, untyped associative arrays.
* 🔧 **Flexible Signatures:** Supports both traditional parameter lists and C#-style request DTO objects.
* ⚡ **Zero External Dependencies:** Built on native PHP CURL for high performance and compatibility.
* 🌐 **Broad Compatibility:** Backwards-compatible down to **PHP 5.4.0** (supports modern PHP 8.x, 7.x, 5.x).

---

## 📦 Installation

Install the package via **Composer**:

```bash
composer require kavenegar/php
```

---

## 🚀 Quick Start

Initialize the client with your API key:

```php
require __DIR__ . '/vendor/autoload.php';

use Kavenegar\KavenegarApi;
use Kavenegar\Exceptions\ApiException;
use Kavenegar\Exceptions\HttpException;

$api = new KavenegarApi("YOUR_API_KEY");

try {
    // Send a simple SMS
    $result = $api->send("10004346", "09123456789", "Hello from Kavenegar PHP SDK!");
    
    foreach ($result as $sms) {
        echo "✅ Sent! Message ID: " . $sms->messageid . "\n";
    }
} catch (ApiException $e) {
    echo "❌ API Error: " . $e->errorMessage() . "\n";
} catch (HttpException $e) {
    echo "❌ Network Error: " . $e->errorMessage() . "\n";
}
```

---

## 📝 DTO Request Objects

For more structured request configuration, you can pass request DTOs directly to the methods:

### Sending SMS with `SendRequest`

```php
use Kavenegar\Models\SendRequest;

$req = new SendRequest();
$req->sender = "10004346";
$req->receptor = "09123456789";
$req->message = "OTP Code: 123456";
$req->policy = "policy_value"; // Optional policy

$result = $api->send($req);
```

### Adding Templates with `TemplateDto` & Enums

```php
use Kavenegar\Models\TemplateDto;
use Kavenegar\Enums\VerificationType;
use Kavenegar\Enums\VerificationUsageType;

$dto = new TemplateDto();
$dto->name = "otp-template-name";
$dto->textmessage = "Your verification code: %token%";
$dto->sourcetype = VerificationUsageType::MobileApplication;
$dto->sendmethod = VerificationType::Message;

$newTemplate = $api->addTemplate($dto);
```

---

## 🤝 Contributing

Contributions, bug reports, and pull requests are welcome!

| | |
|---|---|
| 🐛 **Bug?** | Open an [issue](../../issues) |
| 💡 **Idea?** | Submit a [pull request](../../pulls) |
| 📧 **Help?** | [support@kavenegar.com](mailto:support@kavenegar.com?Subject=SDK) |

---

<br/>

<div dir="rtl">

## 🇮🇷 راهنما

### معرفی سرویس کاوه نگار

کاوه نگار یک وب سرویس ارسال و دریافت پیامک و تماس صوتی است که به راحتی میتوانید از آن استفاده نمایید.

### ساخت حساب کاربری

اگر در وب سرویس کاوه نگار عضو نیستید میتوانید از [لینک عضویت](http://panel.kavenegar.com/client/membership/register) ثبت نام و اکانت آزمایشی برای تست API دریافت نمایید.

### مستندات

برای مشاهده اطلاعات کامل مستندات [وب سرویس پیامک](http://kavenegar.com/وب-سرویس-پیامک.html) به صفحه [مستندات وب سرویس](http://kavenegar.com/rest.html) مراجعه نمایید.

</div>
