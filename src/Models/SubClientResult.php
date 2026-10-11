<?php

namespace Kavenegar\Models;

class SubClientResult
{
    public $remaincredit;
    public $expiredate;
    public $apikey;
    public $fullname;
    public $username;
    public $pricingname;
    public $smsfarsicost;
    public $smsenglishcost;
    public $calllocalcost;
    public $lastlogindate;
    public $assignedlines;
    public $mininumallowedcredit;
    public $status;
    public $pwdhashed;
    public $mobile;
    public $localid;

    public function __construct($data)
    {
        $this->remaincredit = isset($data->remaincredit) ? $data->remaincredit : null;
        $this->expiredate = isset($data->expiredate) ? $data->expiredate : null;
        $this->apikey = isset($data->apikey) ? $data->apikey : null;
        $this->fullname = isset($data->fullname) ? $data->fullname : null;
        $this->username = isset($data->username) ? $data->username : null;
        $this->pricingname = isset($data->pricingname) ? $data->pricingname : null;
        $this->smsfarsicost = isset($data->smsfarsicost) ? $data->smsfarsicost : null;
        $this->smsenglishcost = isset($data->smsenglishcost) ? $data->smsenglishcost : null;
        $this->calllocalcost = isset($data->calllocalcost) ? $data->calllocalcost : null;
        $this->lastlogindate = isset($data->lastlogindate) ? $data->lastlogindate : null;
        $this->assignedlines = isset($data->assignedlines) ? $data->assignedlines : null;
        $this->mininumallowedcredit = isset($data->mininumallowedcredit) ? $data->mininumallowedcredit : null;
        $this->status = isset($data->status) ? $data->status : null;
        $this->pwdhashed = isset($data->pwdhashed) ? $data->pwdhashed : null;
        $this->mobile = isset($data->mobile) ? $data->mobile : null;
        $this->localid = isset($data->localid) ? $data->localid : null;
    }
}
