<?php

namespace Kavenegar\Models;

class CountInboxResult
{
    public $startdate;
    public $enddate;
    public $sumcount;

    public function __construct($data)
    {
        $this->startdate = isset($data->startdate) ? $data->startdate : null;
        $this->enddate = isset($data->enddate) ? $data->enddate : null;
        $this->sumcount = isset($data->sumcount) ? $data->sumcount : null;
    }
}
