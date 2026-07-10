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
     * Not a full resourceful controller - research has no independent
     * index/show/edit views. It only ever renders inside the matter
     * workspace's Research tab, so a single store action is all this needs.
     */
    public function store(Request $request, Matter $matter): RedirectResponse
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'max:1000'],
        ]);

        $session = $this->research->answerQuestion(
            matter: $matter,
            question: $validated['query'],
            userId: $request->user()->id,
        );

        return redirect()
            ->route('matters.show', ['matter' => $matter, 'tab' => 'research'])
            ->with('status', 'Research answer saved.')
            ->with('latest_research_session_id', $session->id);
    }
}
