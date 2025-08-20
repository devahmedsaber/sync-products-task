<?php

namespace App\Repositories;

use App\Models\SyncLog;

class SyncLogRepository
{
    public function increment(int $logId, string $field): void
    {
        SyncLog::where('id', $logId)->increment($field);
    }

    public function updateStatus(int $logId, string $status): void
    {
        SyncLog::where('id', $logId)->update(['status' => $status]);
    }
}
