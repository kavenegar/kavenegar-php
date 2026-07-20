<?php

namespace Kavenegar\Models;

class StatusLocalMessageIdResult
{
    public $messageid;
    public $localid;
    public $status;
    public $statustext;

    public function __construct($data)
    {
        $this->messageid = isset($data->messageid) ? $data->messageid : null;
        $this->localid = isset($data->localid) ? $data->localid : null;
        $this->status = isset($data->status) ? $data->status : null;
        $this->statustext = isset($data->statustext) ? $data->statustext : null;
    }
}
