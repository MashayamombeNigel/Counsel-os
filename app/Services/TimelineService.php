<?php

namespace App\Services;

use App\Models\Matter;
use App\Models\ActivityLog;

class TimelineService
{
    public function recordMatterEvent(Matter $matter, string $action, string $description): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => auth()->id() ?? 1, // Fallback to 1 if no auth user (e.g. testing)
            'matter_id' => $matter->id,
            'subject_type' => Matter::class,
            'subject_id' => $matter->id,
            'action' => $action,
            'description' => $description,
        ]);
    }
}
