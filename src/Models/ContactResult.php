<?php

namespace Kavenegar\Models;

class ContactResult
{
    public $contactid;
    public $groupid;
    public $number;
    public $title;
    public $birthdate;
    public $email;
    public $tags;

    public function __construct($data)
    {
        $this->contactid = isset($data->contactid) ? $data->contactid : null;
        $this->groupid = isset($data->groupid) ? $data->groupid : null;
        $this->number = isset($data->number) ? $data->number : null;
        $this->title = isset($data->title) ? $data->title : null;
        $this->birthdate = isset($data->birthdate) ? $data->birthdate : null;
        $this->email = isset($data->email) ? $data->email : null;
        $this->tags = isset($data->tags) ? $data->tags : null;
    }
}
