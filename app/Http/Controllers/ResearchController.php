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
     * Ask a matter-scoped research question and save the session.
     * Route: POST /matters/{matter}/research
     *
     * Runs synchronously (no queue) per decision - research is
     * conversational and a queue+refresh step would hurt the UX.
     * Since there's no queue safety net here, a Gemini failure is
     * caught directly and surfaced as a flash error rather than
     * silently losing the user's question.
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
