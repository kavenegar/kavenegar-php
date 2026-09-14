<?php

namespace Kavenegar;

use Kavenegar\Exceptions\ApiException;
use Kavenegar\Exceptions\HttpException;
use Kavenegar\Exceptions\RuntimeException;

class KavenegarApi
{
    const APIPATH = "%s://api.kavenegar.com/v1/%s/%s/%s.json/";
    const VERSION = "1.2.2";

    protected $apiKey;
    protected $insecure;
    protected $baseUrl;
    protected $curlHandle = null;
    protected $sslVerify = false;

    public function __construct($apiKey, $insecure = false, $baseUrl = null, $sslVerify = false)
    {
        if (!extension_loaded('curl')) {
            die('cURL library is not loaded');
            exit;
        }
        if (is_null($apiKey)) {
            die('apiKey is empty');
            exit;
        }
        $this->apiKey = trim($apiKey);
        $this->insecure = (bool)$insecure;
        $this->baseUrl = $baseUrl;
        $this->sslVerify = (bool)$sslVerify;
    }

    public function setSslVerify($sslVerify)
    {
        $this->sslVerify = (bool)$sslVerify;
        return $this;
    }

    public function getSslVerify()
    {
        return $this->sslVerify;
    }

    public function __destruct()
    {
        $this->close();
    }

    public function close()
    {
        if ($this->curlHandle !== null) {
            if (is_resource($this->curlHandle) || $this->curlHandle instanceof \CurlHandle) {
                curl_close($this->curlHandle);
            }
            $this->curlHandle = null;
        }
    }

    protected function get_path($method, $base = 'sms')
    {
        if ($this->baseUrl) {
            return rtrim($this->baseUrl, '/') . "/v1/{$this->apiKey}/{$base}/{$method}.json/";
        }
        return sprintf(self::APIPATH, $this->insecure == true ? "http" : "https", $this->apiKey, $base, $method);
    }

    protected function execute($url, $data = null, $method = 'POST')
    {
        $headers = array(
            'Accept: application/json',
            'charset: utf-8',
            'User-Agent: Kavenegar-PHP/' . self::VERSION,
            'Connection: keep-alive'
        );

        $isMultipart = false;
        if (is_array($data)) {
            foreach ($data as $val) {
                if ($val instanceof \CURLFile || (class_exists('CURLStringFile') && is_a($val, 'CURLStringFile'))) {
                    $isMultipart = true;
                    break;
                }
            }
        }

        if (!$isMultipart) {
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        }

        $fields_string = "";
        if (!is_null($data)) {
            if ($isMultipart) {
                $fields_string = $data;
            } else {
                $fields_string = http_build_query($data);
            }
        }

        if ($this->curlHandle === null) {
            $this->curlHandle = curl_init();
        } else {
            if (function_exists('curl_reset')) {
                curl_reset($this->curlHandle);
            }
        }
        $handle = $this->curlHandle;

        $method = strtoupper($method);
        if ($method === 'GET' || $method === 'DELETE') {
            if ($fields_string && is_string($fields_string)) {
                $url .= (strpos($url, '?') === false ? '?' : '&') . $fields_string;
            }
            curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($handle, CURLOPT_HTTPGET, true);
        } else {
            curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($handle, CURLOPT_POST, true);
            curl_setopt($handle, CURLOPT_POSTFIELDS, $fields_string);
        }

        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);

        // SSL verification
        $verifySsl = (bool)$this->sslVerify;
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, $verifySsl);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, $verifySsl ? 2 : 0);

        // TCP Keep-Alive
        if (defined('CURLOPT_TCP_KEEPALIVE')) {
            curl_setopt($handle, CURLOPT_TCP_KEEPALIVE, 1);
            if (defined('CURLOPT_TCP_KEEPIDLE')) {
                curl_setopt($handle, CURLOPT_TCP_KEEPIDLE, 120);
            }
            if (defined('CURLOPT_TCP_KEEPINTVL')) {
                curl_setopt($handle, CURLOPT_TCP_KEEPINTVL, 60);
            }
        }

        $response     = curl_exec($handle);
        $code         = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $curl_errno   = curl_errno($handle);
        $curl_error   = curl_error($handle);

        if ($curl_errno) {
            $this->close();
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
            return $json_response->entries;
        }
    }

    private function map($entries, $modelClass)
    {
        if (is_array($entries)) {
            $mapped = [];
            foreach ($entries as $entry) {
                $mapped[] = new $modelClass($entry);
            }
            return $mapped;
        } elseif (is_object($entries)) {
            return new $modelClass($entries);
        }
        return $entries;
    }

    private function checkDate($date)
    {
        if ($date instanceof \DateTime || $date instanceof \DateTimeInterface) {
            return $date->getTimestamp();
        }
        return $date;
    }

    private function getMimeType($fileName)
    {
        if (empty($fileName)) {
            return "application/octet-stream";
        }
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        switch ($extension) {
            case "jpg":
            case "jpeg": return "image/jpeg";
            case "png": return "image/png";
            case "gif": return "image/gif";
            case "bmp": return "image/bmp";
            case "webp": return "image/webp";
            case "mp4": return "video/mp4";
            case "pdf": return "application/pdf";
            case "txt": return "text/plain";
            default: return "application/octet-stream";
        }
    }

    public function send($sender, $receptor = null, $message = null, $date = null, $type = null, $localid = null, $tag = null, $text = null, $hide = null, $localmessageid = null, $policy = null, $mediaid = null)
    {
        if ($sender instanceof \Kavenegar\Models\SendRequest) {
            $req = $sender;
            $sender = $req->sender;
            $receptor = $req->receptor;
            $message = $req->message;
            $date = $req->date;
            $type = $req->type;
            $localid = $req->localids;
            $tag = $req->tag;
            $text = $req->text;
            $hide = $req->hide;
            $localmessageid = $req->localmessageid;
            $policy = $req->policy;
            $mediaid = $req->mediaid;
        }

        if (is_array($receptor)) {
            $receptor = implode(",", $receptor);
        }
        if (is_array($localid)) {
            $localid = implode(",", $localid);
        }
        $path   = $this->get_path("send");
        $params = array(
            "receptor" => $receptor,
            "sender" => $sender,
            "message" => $message,
            "date" => $this->checkDate($date),
            "type" => $type,
            "localid" => $localid
        );
        if (!is_null($tag)) {
            $params["tag"] = $tag;
        }
        if (!is_null($text)) {
            $params["text"] = $text;
        }
        if (!is_null($hide)) {
            $params["hide"] = $hide;
        }
        if (!is_null($localmessageid)) {
            $params["localmessageid"] = $localmessageid;
        }
        if (!is_null($policy)) {
            $params["policy"] = $policy;
        }
        if (!is_null($mediaid)) {
            $params["mediaid"] = $mediaid;
        }
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\SendResult');
    }

    public function sendArray($sender, $receptor = null, $message = null, $date = null, $type = null, $localmessageid = null, $tag = null, $hide = null, $policy = null, $mediaid = null)
    {
        if ($sender instanceof \Kavenegar\Models\SendArrayRequest) {
            $req = $sender;
            $sender = $req->senders;
            $receptor = $req->receptors;
            $message = $req->messages;
            $date = $req->date;
            $type = $req->types;
            $localmessageid = $req->localmessageids;
            $tag = $req->tag;
            $hide = $req->hide;
            $policy = $req->policy;
            $mediaid = $req->mediaid;
        }

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
        $params = array(
            "receptor" => json_encode($receptor),
            "sender" => json_encode($sender),
            "message" => json_encode($message),
            "date" => $this->checkDate($date),
            "type" => json_encode($type),
            "localmessageid" => json_encode($localmessageid)
        );
        if (!is_null($tag)) {
            $params["tag"] = $tag;
        }
        if (!is_null($hide)) {
            $params["hide"] = $hide;
        }
        if (!is_null($policy)) {
            $params["policy"] = $policy;
        }
        if (!is_null($mediaid)) {
            $params["mediaid"] = $mediaid;
        }
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\SendResult');
    }

    public function status($messageid)
    {
        $path = $this->get_path("status");
        $params = array(
            "messageid" => is_array($messageid) ? implode(",", $messageid) : $messageid
        );
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\StatusResult');
    }

    public function statusLocalMessageId($localid)
    {
        $path = $this->get_path("statuslocalmessageid");
        $params = array(
            "localid" => is_array($localid) ? implode(",", $localid) : $localid
        );
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\StatusLocalMessageIdResult');
    }

    public function select($messageid)
    {
        $path = $this->get_path("select");
        $params = array(
            "messageid" => is_array($messageid) ? implode(",", $messageid) : $messageid
        );
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\SendResult');
    }

    public function selectOutbox($startdate, $enddate = null, $sender = null)
    {
        $path   = $this->get_path("selectoutbox");
        $params = array(
            "startdate" => $this->checkDate($startdate),
            "enddate" => $this->checkDate($enddate),
            "sender" => $sender
        );
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\SendResult');
    }

    public function latestOutbox($pagesize, $sender = null)
    {
        $path   = $this->get_path("latestoutbox");
        $params = array(
            "pagesize" => $pagesize,
            "sender" => $sender
        );
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\SendResult');
    }

    public function countOutbox($startdate, $enddate = null, $status = 0)
    {
        $path   = $this->get_path("countoutbox");
        $params = array(
            "startdate" => $this->checkDate($startdate),
            "enddate" => $this->checkDate($enddate),
            "status" => $status
        );
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\CountOutboxResult');
    }

    public function cancel($messageid)
    {
        $path = $this->get_path("cancel");
        $params = array(
            "messageid" => is_array($messageid) ? implode(",", $messageid) : $messageid
        );
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\StatusResult');
    }

    public function receive($linenumber, $isread = 0)
    {
        $path   = $this->get_path("receive");
        $params = array(
            "linenumber" => $linenumber,
            "isread" => $isread
        );
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\ReceiveResult');
    }

    public function countInbox($startdate, $enddate = null, $linenumber = null, $isread = 0)
    {
        $path   = $this->get_path("countinbox");
        $params = array(
            "startdate" => $this->checkDate($startdate),
            "enddate" => $this->checkDate($enddate),
            "linenumber" => $linenumber,
            "isread" => $isread
        );
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\CountInboxResult');
    }

    public function statusByReceptor($receptor, $startdate = null, $enddate = null)
    {
        $path = $this->get_path("statusbyreceptor");
        $params = array(
            "receptor" => $receptor
        );
        if (!is_null($startdate)) {
            $params["startdate"] = $this->checkDate($startdate);
        }
        if (!is_null($enddate)) {
            $params["enddate"] = $this->checkDate($enddate);
        }
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\StatusResult');
    }

    public function unreads($linenumber, $isread = 0)
    {
        $path = $this->get_path("unreads");
        $params = array(
            "linenumber" => $linenumber,
            "isread" => $isread
        );
        return $this->map($this->execute($path, $params, 'GET'), '\Kavenegar\Models\ReceiveResult');
    }

    public function inboxPaged($linenumber, $isread = null, $startdate = null, $enddate = null, $pagenumber = null)
    {
        $path = $this->get_path("inboxpaged");
        $params = array(
            "linenumber" => $linenumber
        );
        if (!is_null($isread)) {
            $params["isread"] = $isread;
        }
        if (!is_null($startdate)) {
            $params["startdate"] = $this->checkDate($startdate);
        }
        if (!is_null($enddate)) {
            $params["enddate"] = $this->checkDate($enddate);
        }
        if (!is_null($pagenumber)) {
            $params["pagenumber"] = $pagenumber;
        }
        return $this->map($this->execute($path, $params, 'GET'), '\Kavenegar\Models\ReceiveResult');
    }

    public function groupSendReport($startdate, $enddate)
    {
        $path = $this->get_path("groupsend", "sms/report");
        $params = array(
            "startdate" => $this->checkDate($startdate),
            "enddate" => $this->checkDate($enddate)
        );
        return $this->map($this->execute($path, $params, 'GET'), '\Kavenegar\Models\GroupSendReportResult');
    }

    // --- Account API ---

    public function accountInfo()
    {
        $path = $this->get_path("info", "account");
        return $this->map($this->execute($path), '\Kavenegar\Models\AccountInfoResult');
    }

    public function accountConfig($apiLogs, $dailyReport, $debugMode, $defaultSender, $minCreditAlarm, $resendFailed)
    {
        $path   = $this->get_path("config", "account");
        $params = array(
            "apilogs" => $apiLogs,
            "dailyreport" => $dailyReport,
            "debug" => $debugMode,
            "defaultsender" => $defaultSender,
            "mincreditalarm" => $minCreditAlarm,
            "resendfailed" => $resendFailed
        );
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\AccountConfigResult');
    }

    // --- Verify Lookup API ---

    public function verifyLookup($receptor, $token, $token2 = null, $token3 = null, $template = null, $type = null, $token10 = null, $token20 = null, $tag = null)
    {
        $args = func_get_args();
        $num = count($args);

        $params = array(
            "receptor" => $receptor,
            "token" => $token,
        );

        if ($num === 3) {
            $params["template"] = $args[2];
        } elseif ($num === 4) {
            if (is_int($args[3]) || $args[3] === 'sms' || $args[3] === 'call') {
                $params["template"] = $args[2];
                $params["type"] = $args[3];
            } else {
                $params["token2"] = $args[2];
                $params["template"] = $args[3];
            }
        } elseif ($num === 5) {
            if (is_int($args[4]) || $args[4] === 'sms' || $args[4] === 'call') {
                $params["token2"] = $args[2];
                $params["template"] = $args[3];
                $params["type"] = $args[4];
            } else {
                $params["token2"] = $args[2];
                $params["token3"] = $args[3];
                $params["template"] = $args[4];
            }
        } elseif ($num === 6) {
            if (is_int($args[5]) || $args[5] === 'sms' || $args[5] === 'call') {
                $params["token2"] = $args[2];
                $params["token3"] = $args[3];
                $params["template"] = $args[4];
                $params["type"] = $args[5];
            } else {
                $params["token2"] = $args[2];
                $params["token3"] = $args[3];
                $params["token10"] = $args[4];
                $params["template"] = $args[5];
            }
        } elseif ($num === 7) {
            $params["token2"] = $args[2];
            $params["token3"] = $args[3];
            $params["token10"] = $args[4];
            $params["template"] = $args[5];
            $params["type"] = $args[6];
        } elseif ($num >= 8) {
            $params["token2"] = $args[2];
            $params["token3"] = $args[3];
            $params["token10"] = $args[4];
            $params["token20"] = $args[5];
            $params["template"] = $args[6];
            $params["type"] = $args[7];
            if (isset($args[8])) {
                $params["tag"] = $args[8];
            }
        }
        
        if (!is_null($tag) && !isset($params["tag"])) {
            $params["tag"] = $tag;
        }

        $path = $this->get_path("lookup", "verify");
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\SendResult');
    }

    // --- Call / TTS API ---

    public function callMakeTTS($receptor, $message, $date = null, $localid = null, $tag = null)
    {
        $path   = $this->get_path("maketts", "call");
        $params = array(
            "receptor" => is_array($receptor) ? implode(",", $receptor) : $receptor,
            "message" => $message,
            "date" => $this->checkDate($date),
            "localid" => is_array($localid) ? implode(",", $localid) : $localid
        );
        if (!is_null($tag)) {
            $params["tag"] = $tag;
        }
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\SendResult');
    }

    // --- Sub-Client / Customer API ---

    public function addClient($apiKey, $ip = null, $localId = null, $credit = null, $userName = null, $password = null, $fullName = null, $planId = null, $mobile = null, $status = null, $mininumAllowedCredit = null, $lines = null, $canUseParentLines = null, $expireDate = null, $enableLink = null, $canCharge = null)
    {
        if (is_array($apiKey)) {
            $params = $apiKey;
        } elseif ($apiKey instanceof \Kavenegar\Models\SubClientDto) {
            $params = (array) $apiKey;
        } else {
            $params = array(
                "apikey" => $apiKey,
                "ip" => $ip,
                "localid" => $localId,
                "credit" => $credit,
                "username" => $userName,
                "password" => $password,
                "fullname" => $fullName,
                "planid" => $planId,
                "mobile" => $mobile,
                "status" => $status,
                "mininumallowedcredit" => $mininumAllowedCredit,
                "lines" => $lines,
                "canuseparentlines" => $canUseParentLines,
                "expiredate" => $expireDate,
                "enablelink" => $enableLink,
                "cancharge" => $canCharge
            );
            $params = array_filter($params, function($v) { return !is_null($v); });
        }
        $path = $this->get_path("add", "client");
        if (isset($params['canuseparentlines'])) {
            $params['canuseparentlines'] = $params['canuseparentlines'] ? 1 : 0;
        }
        if (isset($params['enablelink'])) {
            $params['enablelink'] = $params['enablelink'] ? 1 : 0;
        }
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\SubClientResult');
    }

    public function updateClient($apiKey, $ip = null, $localId = null, $credit = null, $userName = null, $password = null, $fullName = null, $planId = null, $mobile = null, $status = null, $mininumAllowedCredit = null, $lines = null, $canUseParentLines = null, $expireDate = null, $enableLink = null, $canCharge = null)
    {
        if (is_array($apiKey)) {
            $params = $apiKey;
        } elseif ($apiKey instanceof \Kavenegar\Models\SubClientDto) {
            $params = (array) $apiKey;
        } else {
            $params = array(
                "apikey" => $apiKey,
                "ip" => $ip,
                "localid" => $localId,
                "credit" => $credit,
                "username" => $userName,
                "password" => $password,
                "fullname" => $fullName,
                "planid" => $planId,
                "mobile" => $mobile,
                "status" => $status,
                "mininumallowedcredit" => $mininumAllowedCredit,
                "lines" => $lines,
                "canuseparentlines" => $canUseParentLines,
                "expiredate" => $expireDate,
                "enablelink" => $enableLink,
                "cancharge" => $canCharge
            );
            $params = array_filter($params, function($v) { return !is_null($v); });
        }
        $path = $this->get_path("update", "client");
        if (isset($params['canuseparentlines'])) {
            $params['canuseparentlines'] = $params['canuseparentlines'] ? 1 : 0;
        }
        if (isset($params['enablelink'])) {
            $params['enablelink'] = $params['enablelink'] ? 1 : 0;
        }
        if (isset($params['cancharge'])) {
            $params['cancharge'] = $params['cancharge'] ? 1 : 0;
        }
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\SubClientResult');
    }

    public function listClients()
    {
        $path = $this->get_path("list", "client");
        return $this->map($this->execute($path, null, 'GET'), '\Kavenegar\Models\SubClientResult');
    }

    public function fetchClient($apiKey)
    {
        $path = $this->get_path("fetch", "client");
        return $this->map($this->execute($path, array("apikey" => $apiKey), 'GET'), '\Kavenegar\Models\SubClientResult');
    }

    public function fetchClientByLocalId($localId)
    {
        $path = $this->get_path("fetchbylocalid", "client");
        return $this->map($this->execute($path, array("localid" => $localId), 'GET'), '\Kavenegar\Models\SubClientResult');
    }

    public function renewClientKey($apiKey = null, $localId = null)
    {
        $path = $this->get_path("renewkey", "client");
        $params = array();
        if (!is_null($apiKey)) {
            $params["apikey"] = $apiKey;
        }
        if (!is_null($localId)) {
            $params["localid"] = $localId;
        }
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\SubClientResult');
    }

    public function setClientStatus($apiKey, $status)
    {
        $path = $this->get_path("setstatus", "client");
        return $this->map($this->execute($path, array("apikey" => $apiKey, "status" => $status)), '\Kavenegar\Models\SubClientResult');
    }

    public function chargeClientCredit($apiKey, $credit, $desc = null, $vat = null, $ip = null)
    {
        $path = $this->get_path("chargecredit", "client");
        $params = array(
            "apikey" => $apiKey,
            "credit" => $credit
        );
        if (!is_null($desc)) {
            $params["desc"] = $desc;
        }
        if (!is_null($vat)) {
            $params["vat"] = $vat;
        }
        if (!is_null($ip)) {
            $params["ip"] = $ip;
        }
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\SubClientResult');
    }

    // --- Line Blocked / Blacklist API ---

    public function addBlockedLine($receptor, $lineNumber)
    {
        $path = $this->get_path("add", "line/blocked");
        return $this->map($this->execute($path, array("receptor" => $receptor, "linenumber" => $lineNumber)), '\Kavenegar\Models\BlacklistResult');
    }

    public function removeBlockedLine($receptor, $lineNumber)
    {
        $path = $this->get_path("remove", "line/blocked");
        return $this->map($this->execute($path, array("receptor" => $receptor, "linenumber" => $lineNumber), 'DELETE'), '\Kavenegar\Models\RemoveBlacklistResult');
    }

    public function listBlockedLines($lineNumber, $pageNumber, $startDate, $blockReason = null)
    {
        $path = $this->get_path("list", "line/blocked");
        $params = array(
            "lineNumber" => $lineNumber,
            "pageNumber" => $pageNumber,
            "StartDate" => $this->checkDate($startDate)
        );
        if (!is_null($blockReason)) {
            $params["blockReason"] = $blockReason;
        }
        return $this->map($this->execute($path, $params, 'GET'), '\Kavenegar\Models\LineBlockListResult');
    }

    public function blockedLineExists($lineNumber, $receptor)
    {
        $path = $this->get_path("exists", "line/blocked");
        return $this->map($this->execute($path, array("lineNumber" => $lineNumber, "receptor" => $receptor), 'GET'), '\Kavenegar\Models\BlacklistResult');
    }

    // --- Verification Template Management API ---

    public function listTemplates($apiKey = null, $localId = null, $page = null)
    {
        $path = $this->get_path("templatelist", "verify");
        $params = array();
        if (!is_null($apiKey)) {
            $params["apikey"] = $apiKey;
        }
        if (!is_null($localId)) {
            $params["localid"] = $localId;
        }
        if (!is_null($page)) {
            $params["page"] = $page;
        }
        return $this->map($this->execute($path, $params, 'GET'), '\Kavenegar\Models\TemplateResult');
    }

    public function cloneTemplate($sourceTemplateId = null, $sourceTemplateName = null, $newTemplateName = null, $apiKey = null, $localId = null)
    {
        $path = $this->get_path("clonetemplate", "verify");
        $params = array();
        if (!is_null($sourceTemplateId)) {
            $params["sourcetemplateid"] = $sourceTemplateId;
        }
        if (!is_null($sourceTemplateName)) {
            $params["sourcetemplatename"] = $sourceTemplateName;
        }
        if (!is_null($newTemplateName)) {
            $params["newtemplatename"] = $newTemplateName;
        }
        if (!is_null($apiKey)) {
            $params["apikey"] = $apiKey;
        }
        if (!is_null($localId)) {
            $params["localid"] = $localId;
        }
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\CloneTemplateResult');
    }

    public function addTemplate($name, $textMessage = null, $sourceType = null, $sendMethod = null, $fallBackMethod = null, $primaryLineNumber = null, $secondaryLineNumber = null, $switchTTL = null, $sourceUrl = null, $sourceName = null, $voiceMessage = null, $localId = null, $apiKey = null)
    {
        if (is_array($name)) {
            $params = $name;
        } elseif ($name instanceof \Kavenegar\Models\TemplateDto) {
            $params = (array) $name;
        } else {
            $params = array(
                "name" => $name,
                "textmessage" => $textMessage,
                "sourcetype" => $sourceType,
                "sendmethod" => $sendMethod,
                "fallbackmethod" => $fallBackMethod,
                "primarylinenumber" => $primaryLineNumber,
                "secondarylinenumber" => $secondaryLineNumber,
                "switchttl" => $switchTTL,
                "sourceurl" => $sourceUrl,
                "sourcename" => $sourceName,
                "voicemessage" => $voiceMessage,
                "localid" => $localId,
                "apikey" => $apiKey
            );
            $params = array_filter($params, function($v) { return !is_null($v); });
        }
        $path = $this->get_path("addtemplate", "verify");
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\TemplateInfoResult');
    }

    public function updateTemplate($templateId, $name = null, $textMessage = null, $sourceType = null, $sendMethod = null, $fallBackMethod = null, $primaryLineNumber = null, $secondaryLineNumber = null, $switchTTL = null, $sourceUrl = null, $sourceName = null, $voiceMessage = null, $localId = null, $apiKey = null)
    {
        if (is_array($name)) {
            $params = $name;
        } elseif ($name instanceof \Kavenegar\Models\TemplateDto) {
            $params = (array) $name;
        } else {
            $params = array(
                "name" => $name,
                "textmessage" => $textMessage,
                "sourcetype" => $sourceType,
                "sendmethod" => $sendMethod,
                "fallbackmethod" => $fallBackMethod,
                "primarylinenumber" => $primaryLineNumber,
                "secondarylinenumber" => $secondaryLineNumber,
                "switchttl" => $switchTTL,
                "sourceurl" => $sourceUrl,
                "sourcename" => $sourceName,
                "voicemessage" => $voiceMessage,
                "localid" => $localId,
                "apikey" => $apiKey
            );
            $params = array_filter($params, function($v) { return !is_null($v); });
        }
        $path = $this->get_path("updatetemplate", "verify");
        $params["templateid"] = $templateId;
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\TemplateInfoResult');
    }

    public function getTemplate($id, $apiKey = null, $localId = null)
    {
        $path = $this->get_path("gettemplate", "verify");
        $params = array("id" => $id);
        if (!is_null($apiKey)) {
            $params["apikey"] = $apiKey;
        }
        if (!is_null($localId)) {
            $params["localid"] = $localId;
        }
        return $this->map($this->execute($path, $params, 'GET'), '\Kavenegar\Models\TemplateResult');
    }

    public function deleteTemplate($id, $apiKey = null, $localId = null)
    {
        $path = $this->get_path("deletetemplate", "verify");
        $params = array("id" => $id);
        if (!is_null($apiKey)) {
            $params["apikey"] = $apiKey;
        }
        if (!is_null($localId)) {
            $params["localid"] = $localId;
        }
        return $this->execute($path, $params, 'DELETE');
    }

    public function removeTemplate($id, $apiKey = null, $localId = null)
    {
        return $this->deleteTemplate($id, $apiKey, $localId);
    }

    // --- Media API ---

    public function uploadMedia($filePath)
    {
        if (!file_exists($filePath)) {
            throw new HttpException("File not found: " . $filePath, 0);
        }
        $mimeType = $this->getMimeType($filePath);
        $fileName = basename($filePath);

        $cfile = new \CURLFile($filePath, $mimeType, $fileName);
        $path = $this->get_path("upload", "media");

        return $this->map($this->execute($path, array("File" => $cfile)), '\Kavenegar\Models\MediaResult');
    }

    public function uploadMediaData($name, $fileBytes)
    {
        $mimeType = $this->getMimeType($name);

        if (class_exists('CURLStringFile')) {
            $className = 'CURLStringFile';
            $cfile = new $className($fileBytes, $name, $mimeType);
        } else {
            // Fallback for PHP < 8.1: write a temporary file
            $tempPath = tempnam(sys_get_temp_dir(), 'kavenegar_');
            file_put_contents($tempPath, $fileBytes);
            $cfile = new \CURLFile($tempPath, $mimeType, $name);
        }

        $res = null;
        $error = null;
        try {
            $path = $this->get_path("upload", "media");
            $res = $this->execute($path, array("File" => $cfile));
        } catch (\Exception $e) {
            $error = $e;
        }

        if (isset($tempPath) && file_exists($tempPath)) {
            @unlink($tempPath);
        }

        if ($error) {
            throw $error;
        }
        return $this->map($res, '\Kavenegar\Models\MediaResult');
    }

    public function listMedia($page = null, $size = null)
    {
        $path = $this->get_path("list", "media");
        $params = array();
        if (!is_null($page)) {
            $params["page"] = $page;
        }
        if (!is_null($size)) {
            $params["size"] = $size;
        }
        return $this->map($this->execute($path, $params, 'GET'), '\Kavenegar\Models\MediaResult');
    }

    public function getMedia($id = null, $fileName = null, $section = null)
    {
        $path = $this->get_path("get", "media");
        $params = array();
        if (!is_null($id)) {
            $params["id"] = $id;
        }
        if (!is_null($fileName)) {
            $params["filename"] = $fileName;
        }
        if (!is_null($section)) {
            $params["section"] = $section;
        }
        return $this->map($this->execute($path, $params, 'GET'), '\Kavenegar\Models\MediaResult');
    }

    public function deleteMedia($id)
    {
        $path = $this->get_path("delete", "media");
        return $this->map($this->execute($path, array("id" => $id), 'DELETE'), '\Kavenegar\Models\MediaDeleteResult');
    }

    // --- Contact & Groups API ---

    public function addContact($groupId, $number, $title = null, $birthdate = null, $email = null, $tags = null)
    {
        $path = $this->get_path("add", "group");
        $params = array(
            "groupid" => $groupId,
            "number" => $number
        );
        if (!is_null($title)) {
            $params["title"] = $title;
        }
        if (!is_null($birthdate)) {
            $params["birthdate"] = $birthdate;
        }
        if (!is_null($email)) {
            $params["email"] = $email;
        }
        if (!is_null($tags)) {
            $params["tags"] = $tags;
        }
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\ContactResult');
    }

    public function addGroup($name, $tag = null)
    {
        $path = $this->get_path("addgroup", "group");
        $params = array("name" => $name);
        if (!is_null($tag)) {
            $params["tag"] = $tag;
        }
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\GroupResult');
    }

    public function removeContact($contactId = null, $mobile = null, $groupId = null)
    {
        $path = $this->get_path("remove", "contact");
        $params = array();
        if (!is_null($contactId)) {
            $params["contactid"] = $contactId;
        }
        if (!is_null($mobile)) {
            $params["mobile"] = $mobile;
        }
        if (!is_null($groupId)) {
            $params["groupid"] = $groupId;
        }
        return $this->map($this->execute($path, $params, 'DELETE'), '\Kavenegar\Models\ContactResult');
    }

    public function removeGroup($groupId)
    {
        $path = $this->get_path("removegroup", "group");
        return $this->map($this->execute($path, array("groupid" => $groupId), 'DELETE'), '\Kavenegar\Models\GroupResult');
    }

    public function listGroups()
    {
        $path = $this->get_path("listofgroups", "group");
        return $this->map($this->execute($path, null, 'GET'), '\Kavenegar\Models\GroupResult');
    }

    public function editGroup($groupId, $groupName = null, $status = null, $tag = null)
    {
        $path = $this->get_path("editgroup", "group");
        $params = array("groupid" => $groupId);
        if(is_null($status)){
            $params["status"] = 1;
        }
        if (!is_null($groupName)) {
            $params["groupname"] = $groupName;
        }
        if (!is_null($status)) {
            $params["status"] = $status;
        }
        if (!is_null($tag)) {
            $params["tag"] = $tag;
        }
        return $this->map($this->execute($path, $params), '\Kavenegar\Models\GroupResult');
    }

    public function searchGroups($tag)
    {
        $path = $this->get_path("search", "group");
        return $this->map($this->execute($path, array("tag" => $tag), 'GET'), '\Kavenegar\Models\GroupResult');
    }

    // --- Utilities API ---

    public function getServerDate()
    {
        $path = $this->get_path("getdate", "utils");
        return $this->map($this->execute($path, null, 'GET'), '\Kavenegar\Models\ServerDateResult');
    }

    public function ping()
    {
        $path = $this->get_path("ping", "utils");
        $res = $this->execute($path, null, 'GET');
        return isset($res->status) ? $res->status : null;
    }
}
