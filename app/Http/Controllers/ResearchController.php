<?php

namespace App\Http\Controllers;

use App\Models\Matter;
use App\Services\ResearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ResearchController extends Controller
{
    public function __construct(
        protected ResearchService $research,
    ) {}

    /**
     * Runs synchronously (no queue) — research is conversational and a queue+poll
     * loop would hurt UX. Gemini failures are caught here and surfaced as flash
     * errors rather than silently dropped.
     */
    public function store(Request $request, Matter $matter): RedirectResponse
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'max:1000'],
        ]);

        try {
            $this->research->answerQuestion(
                matter: $matter,
                question: $validated['query'],
                userId: $request->user()->id,
            );

            return redirect()
                ->route('matters.show', ['matter' => $matter, 'tab' => 'research'])
                ->with('status', 'Research answer saved.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('matters.show', ['matter' => $matter, 'tab' => 'research'])
                ->with('error', 'Research request failed: ' . $e->getMessage())
                ->withInput();
        }
    }
}
