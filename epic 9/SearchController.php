<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __construct(
        protected SearchService $search,
    ) {}

    /**
     * Route: GET /search
     * Replaces the old DashboardController::search() method - this
     * is now the dedicated search page the spec's Epic 9 ticket calls
     * for, with real full-text results instead of title-only matching.
     */
    public function index(Request $request): View
    {
        $term = $request->query('q');

        return view('search.results', [
            'term' => $term,
            'results' => $term ? $this->search->search($term) : null,
        ]);
    }
}
