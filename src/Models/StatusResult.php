<?php

namespace Kavenegar\Models;

class StatusResult
{
    public $messageid;
    public $status;
    public $statustext;
    public $receptor;

    public function __construct($data)
    {
        $this->messageid = isset($data->messageid) ? $data->messageid : null;
        $this->status = isset($data->status) ? $data->status : null;
        $this->statustext = isset($data->statustext) ? $data->statustext : null;
        $this->receptor = isset($data->receptor) ? $data->receptor : null;
    }
}
