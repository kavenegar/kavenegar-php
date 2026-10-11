<?php

namespace Kavenegar\Models;

class BlockedLineDetail
{
    public $number;
    public $blockreason;
    public $date;

    public function __construct($data)
    {
        $this->number = isset($data->number) ? $data->number : null;
        $this->blockreason = isset($data->blockreason) ? $data->blockreason : null;
        $this->date = isset($data->date) ? $data->date : null;
    }
}
