<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentStoreRequest;
use App\Http\Requests\DocumentUpdateRequest;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(Request $request): Response
    {
        $documents = Document::all();

        return view('document.index', [
            'documents' => $documents,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('document.create');
    }

    public function store(DocumentStoreRequest $request): Response
    {
        $document = Document::create($request->validated());

        $request->session()->flash('document.id', $document->id);

        return redirect()->route('documents.index');
    }

    public function show(Request $request, Document $document): Response
    {
        return view('document.show', [
            'document' => $document,
        ]);
    }

    public function edit(Request $request, Document $document): Response
    {
        return view('document.edit', [
            'document' => $document,
        ]);
    }

    public function update(DocumentUpdateRequest $request, Document $document): Response
    {
        $document->update($request->validated());

        $request->session()->flash('document.id', $document->id);

        return redirect()->route('documents.index');
    }

    public function destroy(Request $request, Document $document): RedirectResponse
    {
        $document->delete();

        return redirect()->route('documents.index');
    }

    public function extract(Request $request, Document $document): RedirectResponse
    {
        // TODO: Call TextExtractionService
        return redirect()->route('documents.show', $document)->with('status', 'Extraction started.');
    }

    public function analyze(Request $request, Document $document): RedirectResponse
    {
        // TODO: Call AiAnalysisService
        return redirect()->route('documents.show', $document)->with('status', 'Analysis started.');
    }
}
