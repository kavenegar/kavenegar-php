<?php

namespace Kavenegar\Models;

class GroupSendReportResult
{
    public $groupid;
    public $total;
    public $failed;
    public $success;

    public function __construct($data)
    {
        $this->groupid = isset($data->groupid) ? $data->groupid : null;
        $this->total = isset($data->total) ? $data->total : null;
        $this->failed = isset($data->failed) ? $data->failed : null;
        $this->success = isset($data->success) ? $data->success : null;
    }
}
