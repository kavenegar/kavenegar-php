<?php

namespace Kavenegar\Models;

class AccountInfoResult
{
    public $remaincredit;
    public $expiredate;
    public $type;

    public function __construct($data)
    {
        $this->remaincredit = isset($data->remaincredit) ? $data->remaincredit : null;
        $this->expiredate = isset($data->expiredate) ? $data->expiredate : null;
        $this->type = isset($data->type) ? $data->type : null;
    }
}
