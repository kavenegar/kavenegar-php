<?php

namespace Kavenegar;

use Kavenegar\Exceptions\ApiException;
use Kavenegar\Exceptions\HttpException;
use Kavenegar\Exceptions\RuntimeException;
use Kavenegar\Enums\ApiLogs ;
use Kavenegar\Enums\General;

class KavenegarApi
{
    const APIPATH = "%s://api.kavenegar.com/v1/%s/%s/%s.json/";
    const VERSION = "1.2.2";
    public function __construct($apiKey,$insecure=false)
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
        $this->insecure = $insecure;
    }   
    
	protected function get_path($method, $base = 'sms')
    {
        return sprintf(self::APIPATH,$this->insecure==true ? "http": "https", $this->apiKey, $base, $method);
    }
	
	protected function execute($url, $data = null)
    {        
        $headers       = array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'charset: utf-8'
        );
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
            return $json_response->entries;
        }
        
    }
    	
    public function Send($sender, $receptor, $message, $date = null, $type = null, $localid = null)
    {
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
            "date" => $date,
            "type" => $type,
            "localid" => $localid
        );
        return $this->execute($path, $params);
    }
    
    public function SendArray($sender, $receptor, $message, $date = null, $type = null, $localmessageid = null)
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
        $params = array(
            "receptor" => json_encode($receptor),
            "sender" => json_encode($sender),
            "message" => json_encode($message),
            "date" => $date,
            "type" => json_encode($type),
            "localmessageid" => json_encode($localmessageid)
        );
        return $this->execute($path, $params);
    }
    
    public function Status($messageid)
    {
        $path = $this->get_path("status");
		$params = array(
            "messageid" => is_array($messageid) ? implode(",", $messageid) : $messageid
        );
        return $this->execute($path,$params);      
    }
    
    public function StatusLocalMessageId($localid)
    {
        $path = $this->get_path("statuslocalmessageid");
		$params = array(
            "localid" => is_array($localid) ? implode(",", $localid) : $localid
        );
        return $this->execute($path, $params);
    }
    
    public function Select($messageid)
    {
		$params = array(
            "messageid" => is_array($messageid) ? implode(",", $messageid) : $messageid
        );
        $path = $this->get_path("select");
        return $this->execute($path, $params);       
    }
    
    public function SelectOutbox($startdate, $enddate, $sender)
    {
        $path   = $this->get_path("selectoutbox");
        $params = array(
            "startdate" => $startdate,
            "enddate" => $enddate,
            "sender" => $sender
        );
        return $this->execute($path, $params);
    }
    
    public function LatestOutbox($pagesize, $sender)
    {
        $path   = $this->get_path("latestoutbox");
        $params = array(
            "pagesize" => $pagesize,
            "sender" => $sender
        );
        return $this->execute($path, $params);
    }
    
    public function CountOutbox($startdate, $enddate, $status = 0)
    {
        $path   = $this->get_path("countoutbox");
        $params = array(
            "startdate" => $startdate,
            "enddate" => $enddate,
            "status" => $status
        );
        return $this->execute($path, $params);
    }
    
    public function Cancel($messageid)
    {
        $path = $this->get_path("cancel");
		$params = array(
            "messageid" => is_array($messageid) ? implode(",", $messageid) : $messageid
        );
        return $this->execute($path,$params);
        
    }
    
    public function Receive($linenumber, $isread = 0)
    {
        $path   = $this->get_path("receive");
        $params = array(
            "linenumber" => $linenumber,
            "isread" => $isread
        );
        return $this->execute($path, $params);
    }
    
    public function CountInbox($startdate, $enddate, $linenumber, $isread = 0)
    {
        $path   = $this->get_path("countinbox");
        $params = array(
            "startdate" => $startdate,
            "enddate" => $enddate,
            "linenumber" => $linenumber,
            "isread" => $isread
        );
        return $this->execute($path, $params);
    }
    
    public function CountPostalcode($postalcode)
    {
        $path   = $this->get_path("countpostalcode");
        $params = array(
            "postalcode" => $postalcode
        );
        return $this->execute($path, $params);
    }
    
    public function SendbyPostalcode($sender,$postalcode,$message, $mcistartindex, $mcicount, $mtnstartindex, $mtncount, $date)
    {
        $path   = $this->get_path("sendbypostalcode");
        $params = array(
            "postalcode" => $postalcode,
            "sender" => $sender,
            "message" => $message,
            "mcistartindex" => $mcistartindex,
            "mcicount" => $mcicount,
            "mtnstartindex" => $mtnstartindex,
            "mtncount" => $mtncount,
            "date" => $date
        );
        return $this->execute($path, $params);
    }
    
    public function AccountInfo()
    {
        $path = $this->get_path("info", "account");
        return $this->execute($path);
    } 
    
    public function AccountConfig($apilogs, $dailyreport, $debug, $defaultsender, $mincreditalarm, $resendfailed)
    {
        $path   = $this->get_path("config", "account");
        $params = array(
            "apilogs" => $apilogs,
            "dailyreport" => $dailyreport,
            "debug" => $debug,
            "defaultsender" => $defaultsender,
            "mincreditalarm" => $mincreditalarm,
            "resendfailed" => $resendfailed
        );
        return $this->execute($path, $params);
    }

    public function VerifyLookup($receptor, $token, $token2, $token3, $template, $type = null)
    {
        $path   = $this->get_path("lookup", "verify");
        $params = array(
            "receptor" => $receptor,
            "token" => $token,
            "token2" => $token2,
            "token3" => $token3,
            "template" => $template,
            "type" => $type
        );
        if(func_num_args()>5){
            $arg_list = func_get_args();
            if(isset($arg_list[6]))
                $params["token10"]=$arg_list[6];
            if(isset($arg_list[7]))
                $params["token20"]=$arg_list[7];
        } 
        return $this->execute($path, $params); 
    }  

    public function CallMakeTTS($receptor, $message, $date = null, $localid = null)
    {
        $path   = $this->get_path("maketts", "call");
        $params = array(
            "receptor" => $receptor,
            "message" => $message,
            "date" => $date,
            "localid" => $localid
        );
        return $this->execute($path, $params); 
    }
}
?>