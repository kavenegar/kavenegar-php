<?php

namespace Kavenegar\Models;

class MediaDeleteResult
{
    public $message;
    public $filename;

    public function __construct($data)
    {
        $this->message = isset($data->message) ? $data->message : null;
        $this->filename = isset($data->filename) ? $data->filename : null;
    }
}
