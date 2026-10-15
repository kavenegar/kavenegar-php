<?php

namespace Kavenegar\Models;

class TemplateInfoResult
{
    public $name;
    public $sourcetype;
    public $sendmethod;
    public $fallbackmethod;
    public $primarylinenumber;
    public $secondarylinenumber;
    public $switchttl;
    public $sourceurl;
    public $sourcename;
    public $textmessage;
    public $voicemessage;
    public $templateid;

    public function __construct($data)
    {
        $this->name = isset($data->name) ? $data->name : null;
        $this->sourcetype = isset($data->sourcetype) ? $data->sourcetype : null;
        $this->sendmethod = isset($data->sendmethod) ? $data->sendmethod : null;
        $this->fallbackmethod = isset($data->fallbackmethod) ? $data->fallbackmethod : null;
        $this->primarylinenumber = isset($data->primarylinenumber) ? $data->primarylinenumber : null;
        $this->secondarylinenumber = isset($data->secondarylinenumber) ? $data->secondarylinenumber : null;
        $this->switchttl = isset($data->switchttl) ? $data->switchttl : null;
        $this->sourceurl = isset($data->sourceurl) ? $data->sourceurl : null;
        $this->sourcename = isset($data->sourcename) ? $data->sourcename : null;
        $this->textmessage = isset($data->textmessage) ? $data->textmessage : null;
        $this->voicemessage = isset($data->voicemessage) ? $data->voicemessage : null;
        $this->templateid = isset($data->templateid) ? $data->templateid : null;
    }
}
