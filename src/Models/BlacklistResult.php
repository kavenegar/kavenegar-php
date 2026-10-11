<?php

namespace Kavenegar\Models;

class BlacklistResult
{
    public $linenumber;
    public $receptor;

    public function __construct($data)
    {
        $this->linenumber = isset($data->linenumber) ? $data->linenumber : null;
        $this->receptor = isset($data->receptor) ? $data->receptor : null;
    }
}
