<?php

namespace Kavenegar\Models;

class RemoveBlacklistResult
{
    public $message;

    public function __construct($data)
    {
        $this->message = isset($data->message) ? $data->message : null;
    }
}
