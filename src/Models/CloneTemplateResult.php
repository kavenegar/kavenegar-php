<?php

namespace Kavenegar\Models;

class CloneTemplateResult
{
    public $id;
    public $name;

    public function __construct($data)
    {
        $this->id = isset($data->id) ? $data->id : null;
        $this->name = isset($data->name) ? $data->name : null;
    }
}
