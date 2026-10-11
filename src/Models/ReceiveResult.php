<?php

namespace Kavenegar\Models;

class ReceiveResult
{
    public $date;
    public $messageid;
    public $sender;
    public $message;
    public $receptor;

    public function __construct($data)
    {
        $this->date = isset($data->date) ? $data->date : null;
        $this->messageid = isset($data->messageid) ? $data->messageid : null;
        $this->sender = isset($data->sender) ? $data->sender : null;
        $this->message = isset($data->message) ? $data->message : null;
        $this->receptor = isset($data->receptor) ? $data->receptor : null;
    }
}
