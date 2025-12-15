<?php

namespace Kavenegar;

use Kavenegar\Exceptions\ApiException;
use Kavenegar\Exceptions\HttpException;
use Kavenegar\Exceptions\RuntimeException;
use Kavenegar\Exceptions\NotProperlyConfiguredException;
use Kavenegar\Enums\ApiLogs;
use Kavenegar\Enums\General;

class KavenegarApi
{
    const APIPATH = "%s://api.kavenegar.com/v1/%s/%s/%s.json/";
    const VERSION = "2.0.0-dev";
    private string $apiKey = "";
    private bool $insecure = false;
    public function __construct(string $apiKey, bool $insecure = false)
    {
        if (!extension_loaded('curl')) {
            throw new NotProperlyConfiguredException('cURL library is not loaded');
        }
        if (empty(trim($apiKey))) {
            throw new NotProperlyConfiguredException('apiKey is empty');
        }
        $this->apiKey = trim($apiKey);
        $this->insecure = $insecure;
    }

	protected function get_path(string $method, string $base = 'sms'): string
    {
        return sprintf(self::APIPATH,$this->insecure==true ? "http": "https", $this->apiKey, $base, $method);
    }

	protected function execute(string $url, ?array $data = null): mixed
    {
        $headers = [
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded; charset=utf-8'
        ];
        $fields_string = "";
        if (!is_null($data)) {
            $fields_string = http_build_query($data);
        }
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $fields_string);

        $response     = curl_exec($handle);
        $code         = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $content_type = curl_getinfo($handle, CURLINFO_CONTENT_TYPE);
        $curl_errno   = curl_errno($handle);
        $curl_error   = curl_error($handle);
        if ($curl_errno) {
            throw new HttpException($curl_error, $curl_errno);
        }
        $json_response = json_decode($response);
        if ($code != 200 && is_null($json_response)) {
            throw new HttpException("Request have errors", $code);
        } else {
            $json_return = $json_response->return;
            if ($json_return->status != 200) {
                throw new ApiException($json_return->message, $json_return->status);
            }
            return $json_response;
        }

    }

    public function Send(string $sender, string|array $receptor, string $message, ?int $date = null, ?int $type = null, string|array|null $localid = null): mixed
    {
        if (is_array($receptor)) {
            $receptor = implode(",", $receptor);
        }
        if (is_array($localid)) {
            $localid = implode(",", $localid);
        }
        $path   = $this->get_path("send");
        $params = [
            "receptor" => $receptor,
            "sender" => $sender,
            "message" => $message,
            "date" => $date,
            "type" => $type,
            "localid" => $localid
        ];
        return $this->execute($path, $params);
    }

    public function SendArray(string|array $sender, string|array $receptor, string|array $message, ?int $date = null, int|array|null $type = null, int|array|null $localmessageid = null): mixed
    {
        if (!is_array($receptor)) {
            $receptor = (array) $receptor;
        }
        if (!is_array($sender)) {
            $sender = (array) $sender;
        }
        if (!is_array($message)) {
            $message = (array) $message;
        }
        $repeat = count($receptor);
        if (!is_null($type) && !is_array($type)) {
            $type = array_fill(0, $repeat, $type);
        }
        if (!is_null($localmessageid) && !is_array($localmessageid)) {
            $localmessageid = array_fill(0, $repeat, $localmessageid);
        }
        $path   = $this->get_path("sendarray");
        $params = [
            "receptor" => json_encode($receptor),
            "sender" => json_encode($sender),
            "message" => json_encode($message),
            "date" => $date,
            "type" => json_encode($type),
            "localmessageid" => json_encode($localmessageid)
        ];
        return $this->execute($path, $params);
    }

    public function Status(string|array $messageid): mixed
    {
        $path = $this->get_path("status");
		$params = [
            "messageid" => is_array($messageid) ? implode(",", $messageid) : $messageid
        ];
        return $this->execute($path,$params);
    }

    public function StatusLocalMessageId(string|array $localid): mixed
    {
        $path = $this->get_path("statuslocalmessageid");
		$params = [
            "localid" => is_array($localid) ? implode(",", $localid) : $localid
        ];
        return $this->execute($path, $params);
    }

    public function Select(string|array $messageid): mixed
    {
		$params = [
            "messageid" => is_array($messageid) ? implode(",", $messageid) : $messageid
        ];
        $path = $this->get_path("select");
        return $this->execute($path, $params);
    }

    public function SelectOutbox(int $startdate, int $enddate, string $sender): mixed
    {
        $path   = $this->get_path("selectoutbox");
        $params = [
            "startdate" => $startdate,
            "enddate" => $enddate,
            "sender" => $sender
        ];
        return $this->execute($path, $params);
    }

    public function LatestOutbox(int $pagesize, string $sender): mixed
    {
        $path   = $this->get_path("latestoutbox");
        $params = [
            "pagesize" => $pagesize,
            "sender" => $sender
        ];
        return $this->execute($path, $params);
    }

    public function CountOutbox(int $startdate, int $enddate, int $status = 0): mixed
    {
        $path   = $this->get_path("countoutbox");
        $params = [
            "startdate" => $startdate,
            "enddate" => $enddate,
            "status" => $status
        ];
        return $this->execute($path, $params);
    }

    public function Cancel(string|array $messageid): mixed
    {
        $path = $this->get_path("cancel");
		$params = [
            "messageid" => is_array($messageid) ? implode(",", $messageid) : $messageid
        ];
        return $this->execute($path,$params);
    }

    public function Receive(string $linenumber, int $isread = 0): mixed
    {
        $path   = $this->get_path("receive");
        $params = [
            "linenumber" => $linenumber,
            "isread" => $isread
        ];
        return $this->execute($path, $params);
    }

    public function CountInbox(int $startdate, int $enddate, string $linenumber, int $isread = 0): mixed
    {
        $path   = $this->get_path("countinbox");
        $params = [
            "startdate" => $startdate,
            "enddate" => $enddate,
            "linenumber" => $linenumber,
            "isread" => $isread
        ];
        return $this->execute($path, $params);
    }

    public function CountPostalcode(string $postalcode): mixed
    {
        $path   = $this->get_path("countpostalcode");
        $params = [
            "postalcode" => $postalcode
        ];
        return $this->execute($path, $params);
    }

    public function SendbyPostalcode(string $sender, string $postalcode, string $message, int $mcistartindex, int $mcicount, int $mtnstartindex, int $mtncount, ?int $date): mixed
    {
        $path   = $this->get_path("sendbypostalcode");
        $params = [
            "postalcode" => $postalcode,
            "sender" => $sender,
            "message" => $message,
            "mcistartindex" => $mcistartindex,
            "mcicount" => $mcicount,
            "mtnstartindex" => $mtnstartindex,
            "mtncount" => $mtncount,
            "date" => $date
        ];
        return $this->execute($path, $params);
    }

    public function AccountInfo(): mixed
    {
        $path = $this->get_path("info", "account");
        return $this->execute($path);
    }

    public function AccountConfig(string $apilogs, string $dailyreport, string $debug, string $defaultsender, int $mincreditalarm, string $resendfailed): mixed
    {
        $path   = $this->get_path("config", "account");
        $params = [
            "apilogs" => $apilogs,
            "dailyreport" => $dailyreport,
            "debug" => $debug,
            "defaultsender" => $defaultsender,
            "mincreditalarm" => $mincreditalarm,
            "resendfailed" => $resendfailed
        ];
        return $this->execute($path, $params);
    }

    public function VerifyLookup(string $receptor, string $token, ?string $token2 = null, ?string $token3 = null, ?string $token10 = null, ?string $token20 = null, string $template = 'verify', ?int $type = null): mixed
    {
        $path   = $this->get_path("lookup", "verify");
        $params = [
            "receptor" => $receptor,
            "token" => $token,
            "token2" => $token2,
            "token3" => $token3,
            "token10" => $token10,
            "token20" => $token20,
            "template" => $template,
            "type" => $type
        ];
        return $this->execute($path, $params);
    }

    public function CallMakeTTS(string $receptor, string $message, ?int $date = null, string|array|null $localid = null): mixed
    {
        $path   = $this->get_path("maketts", "call");
        $params = [
            "receptor" => $receptor,
            "message" => $message,
            "date" => $date,
            "localid" => $localid
        ];
        return $this->execute($path, $params);
    }
}
