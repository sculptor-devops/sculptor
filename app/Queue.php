<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    public function ok(): bool
    {
        return $this->status == QUEUE_STATUS_OK;
    }
}
