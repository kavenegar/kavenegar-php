<?php

namespace Kavenegar\Models;

class ServerDateResult
{
    public $datetime;
    public $year;
    public $month;
    public $day;
    public $hour;
    public $minute;
    public $second;
    public $unixtime;

    public function __construct($data)
    {
        $this->datetime = isset($data->datetime) ? $data->datetime : null;
        $this->year = isset($data->year) ? $data->year : null;
        $this->month = isset($data->month) ? $data->month : null;
        $this->day = isset($data->day) ? $data->day : null;
        $this->hour = isset($data->hour) ? $data->hour : null;
        $this->minute = isset($data->minute) ? $data->minute : null;
        $this->second = isset($data->second) ? $data->second : null;
        $this->unixtime = isset($data->unixtime) ? $data->unixtime : null;
    }
}
