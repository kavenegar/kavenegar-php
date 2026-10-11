<?php

namespace Kavenegar\Models;

class MediaListResult
{
    public $metadata;
    public $entries;

    public function __construct($data)
    {
        $this->metadata = isset($data->metadata) ? (array) $data->metadata : [];
        $this->entries = [];
        if (isset($data->entries) && is_array($data->entries)) {
            foreach ($data->entries as $entry) {
                $this->entries[] = new MediaResult($entry);
            }
        }
    }
}
