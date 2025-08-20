<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    protected $fillable = [
        'fetched',
        'created',
        'updated',
        'skipped',
        'failed',
        'status',
    ];
}
