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
     * Status is flipped to 'extracting' synchronously before dispatch so the UI
     * reflects the change immediately rather than waiting for the queue worker.
     * 'failed' is a valid starting state here — this is the retry path.
     */
    public function extract(Document $document): RedirectResponse
    {
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
     * Mirrors extract(): status flips synchronously before dispatch.
     * 'failed' is a valid starting state — this is the retry path.
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
