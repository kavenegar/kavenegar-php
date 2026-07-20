<?php

namespace Kavenegar\Models;

class GroupResult
{
    public $groupid;
    public $name;

    public function __construct($data)
    {
        $this->groupid = isset($data->groupid) ? $data->groupid : null;
        $this->name = isset($data->name) ? $data->name : null;
    }
}
