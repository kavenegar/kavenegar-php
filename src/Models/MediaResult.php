<?php

namespace Kavenegar\Models;

class MediaResult
{
    public $id;
    public $title;
    public $filename;
    public $size;
    public $duration;
    public $createddate;

    public function __construct($data)
    {
        $this->id = isset($data->id) ? $data->id : null;
        $this->title = isset($data->title) ? $data->title : null;
        $this->filename = isset($data->filename) ? $data->filename : null;
        $this->size = isset($data->size) ? $data->size : null;
        $this->duration = isset($data->duration) ? $data->duration : null;
        $this->createddate = isset($data->createddate) ? $data->createddate : null;
    }
}
