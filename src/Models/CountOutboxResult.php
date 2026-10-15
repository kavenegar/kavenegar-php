<?php

namespace Kavenegar\Models;

class CountOutboxResult
{
    public $startdate;
    public $enddate;
    public $sumpart;
    public $sumcount;
    public $cost;

    public function __construct($data)
    {
        $this->startdate = isset($data->startdate) ? $data->startdate : null;
        $this->enddate = isset($data->enddate) ? $data->enddate : null;
        $this->sumpart = isset($data->sumpart) ? $data->sumpart : null;
        $this->sumcount = isset($data->sumcount) ? $data->sumcount : null;
        $this->cost = isset($data->cost) ? $data->cost : null;
    }
}
