<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Document;
use App\Models\Matter;
use Illuminate\Support\Facades\Auth;

class TimelineService
{
    /**
     * Records a matter-level event (created, status changed, etc).
     * NOTE: if your Epic 0 stub already has this method with a
     * different signature, reconcile the two rather than duplicating -
     * this version is shown for completeness since MatterService
     * depends on it.
     */
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
     * Records a document-level event (uploaded, extracted, failed).
     * Logged against the parent matter_id so it shows up in the
     * matter workspace's Timeline tab alongside matter-level events,
     * per spec Section 9 ("Timeline event created" after extraction).
     *
     * IMPORTANT: this is called both from HTTP request context
     * (DocumentController::store, where Auth::id() works fine) and
     * from inside ExtractDocumentTextJob running on the queue worker,
     * where there is no authenticated session and Auth::id() would
     * silently return null. Rather than let that null slip through,
     * $userId defaults to the document's uploaded_by so queued job
     * calls still attribute the log entry to a real user.
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
