<?php

namespace Kavenegar\Models;

class SendResult
{
    public $messageid;
    public $cost;
    public $date;
    public $message;
    public $receptor;
    public $sender;
    public $status;
    public $statustext;

    public function __construct($data)
    {
        $this->messageid = isset($data->messageid) ? $data->messageid : null;
        $this->cost = isset($data->cost) ? $data->cost : null;
        $this->date = isset($data->date) ? $data->date : null;
        $this->message = isset($data->message) ? $data->message : null;
        $this->receptor = isset($data->receptor) ? $data->receptor : null;
        $this->sender = isset($data->sender) ? $data->sender : null;
        $this->status = isset($data->status) ? $data->status : null;
        $this->statustext = isset($data->statustext) ? $data->statustext : null;
    }
}
