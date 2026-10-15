<?php

namespace Kavenegar\Models;

class AccountConfigResult
{
    public $apilogs;
    public $dailyreport;
    public $debug;
    public $defaultsender;
    public $mincreditalarm;
    public $resendfailed;

    public function __construct($data)
    {
        $this->apilogs = isset($data->apilogs) ? $data->apilogs : null;
        $this->dailyreport = isset($data->dailyreport) ? $data->dailyreport : null;
        $this->debug = isset($data->debug) ? $data->debug : null;
        $this->defaultsender = isset($data->defaultsender) ? $data->defaultsender : null;
        $this->mincreditalarm = isset($data->mincreditalarm) ? $data->mincreditalarm : null;
        $this->resendfailed = isset($data->resendfailed) ? $data->resendfailed : null;
    }
}
