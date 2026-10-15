<?php

namespace Kavenegar\Models;

class TemplateResult
{
    public $id;
    public $name;
    public $smsmessage;
    public $callmessage;
    public $primarylinenumber;
    public $secondarylinenumber;
    public $sendpriority;
    public $switchttl;
    public $approvalstatus;

    public function __construct($data)
    {
        $this->id = isset($data->id) ? $data->id : null;
        $this->name = isset($data->name) ? $data->name : null;
        $this->smsmessage = isset($data->smsmessage) ? $data->smsmessage : null;
        $this->callmessage = isset($data->callmessage) ? $data->callmessage : null;
        $this->primarylinenumber = isset($data->primarylinenumber) ? $data->primarylinenumber : null;
        $this->secondarylinenumber = isset($data->secondarylinenumber) ? $data->secondarylinenumber : null;
        $this->sendpriority = isset($data->sendpriority) ? $data->sendpriority : null;
        $this->switchttl = isset($data->switchttl) ? $data->switchttl : null;
        $this->approvalstatus = isset($data->approvalstatus) ? $data->approvalstatus : null;
    }
}
