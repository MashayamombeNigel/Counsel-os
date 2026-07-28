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

    public function index(Request $request): View
    {
        $term = $request->query('q');

        return view('search.results', [
            'term'    => $term,
            'results' => $term ? $this->search->search($term) : null,
        ]);
    }
}
