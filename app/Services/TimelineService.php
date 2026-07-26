<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Document;
use App\Models\Matter;
use Illuminate\Support\Facades\Auth;

class TimelineService
{
    public function recordMatterEvent(Matter $matter, string $action, string $description): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'matter_id' => $matter->id,
            'subject_type' => Matter::class,
            'subject_id' => $matter->id,
            'action' => $action,
            'description' => $description,
        ]);
    }

    /**
     * Called from both HTTP context (DocumentController::store) and queued jobs
     * (ExtractDocumentTextJob), where Auth::id() returns null. $userId defaults to
     * $document->uploaded_by so queued calls still attribute the log to a real user.
     */
    public function recordDocumentEvent(Document $document, string $action, string $description, ?int $userId = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $userId ?? Auth::id() ?? $document->uploaded_by,
            'matter_id' => $document->matter_id,
            'subject_type' => Document::class,
            'subject_id' => $document->id,
            'action' => $action,
            'description' => $description,
        ]);
    }
}
