<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Matter;
use App\Http\Requests\UploadDocumentRequest;
use App\Jobs\ExtractDocumentTextJob;
use App\Jobs\AnalyzeDocumentJob;
use App\Services\DocumentService;
use App\Services\TimelineService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function __construct(
        protected DocumentService $documents,
        protected TimelineService $timeline,
    ) {}

    /**
     * Store a newly uploaded document against a matter.
     * Route: POST /matters/{matter}/documents
     */
    public function store(UploadDocumentRequest $request, Matter $matter): RedirectResponse
    {
        $document = $this->documents->storeUpload(
            matter: $matter,
            file: $request->file('file'),
            documentType: $request->validated('document_type'),
            uploadedBy: $request->user()->id,
        );

        $this->timeline->recordDocumentEvent(
            document: $document,
            action: 'document_uploaded',
            description: "Document \"{$document->original_name}\" uploaded.",
        );

        return redirect()
            ->route('documents.show', $document)
            ->with('status', 'Document uploaded. Click "Run Extraction" when ready.');
    }

    /**
     * Show a single document with its extracted text preview and
     * insights if analysis has completed.
     * Route: GET /documents/{document}
     */
    public function show(Document $document): View
    {
        $document->load('matter', 'documentInsight');

        return view('documents.show', [
            'document' => $document,
            'insight' => $document->documentInsight,
        ]);
    }

    public function destroy(Document $document): RedirectResponse
    {
        $matter = $document->matter;
        $this->documents->delete($document);

        return redirect()
            ->route('matters.show', $matter)
            ->with('status', 'Document removed.');
    }

    /**
     * Dispatches the queued extraction job. Status flips to
     * "extracting" immediately (synchronously, here) so the UI
     * reflects the change right away instead of waiting for the
     * queue worker to actually pick up the job - otherwise the page
     * would still show "Uploaded" for however long the job sits
     * queued, which reads as broken even when it isn't.
     * Route: POST /documents/{document}/extract
     */
    public function extract(Document $document): RedirectResponse
    {
        // 'failed' is a valid starting point too - this is the retry
        // path referenced in spec Section 9's status table ("Retry
        // analysis action" for the failed state).
        if (! in_array($document->processing_status, ['uploaded', 'failed'], true)) {
            return redirect()
                ->route('documents.show', $document)
                ->with('error', 'This document has already been extracted or is currently processing.');
        }

        $document->update(['processing_status' => 'extracting', 'error_message' => null]);

        ExtractDocumentTextJob::dispatch($document);

        return redirect()
            ->route('documents.show', $document)
            ->with('status', 'Extraction queued. Refresh in a moment to see the result.');
    }

    /**
     * Dispatches queued Gemini analysis. Mirrors extract()'s pattern:
     * status flips to a processing state immediately so the UI
     * reflects the click right away, and 'failed' is a valid retry
     * starting point (this closes the gap flagged during Epic 3 -
     * the old synchronous version had no retry path from failed).
     * Route: POST /documents/{document}/analyze
     */
    public function analyze(Document $document): RedirectResponse
    {
        if (empty($document->extracted_text)) {
            return redirect()
                ->route('documents.show', $document)
                ->with('error', 'No extracted text available. Run extraction first.');
        }

        if (! in_array($document->processing_status, ['analysis_pending', 'failed'], true)) {
            return redirect()
                ->route('documents.show', $document)
                ->with('error', 'This document has already been analyzed or is currently processing.');
        }

        $document->update(['error_message' => null]);

        AnalyzeDocumentJob::dispatch($document);

        return redirect()
            ->route('documents.show', $document)
            ->with('status', 'Analysis queued. Refresh in a moment to see the result.');
    }
}
