<?php

namespace App\Http\Controllers;

use App\Models\Matter;
use App\Models\Document;
use App\Models\Task;
use App\Models\ActivityLog;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Route: GET /dashboard
     * Response: counts for open matters, recent documents, upcoming
     * tasks, and latest activity - per spec US-A2. Empty states are
     * handled in the Blade view, not here.
     *
     * NOTE: this replaces a Blueprint-generated stub that referenced
     * a non-existent Dashboard::all() - Dashboard isn't a domain
     * model, it's an aggregate view over Matter/Document/Task/
     * ActivityLog, so this controller queries those directly.
     */
    public function index(): View
    {
        return view('dashboard', [
            'openMattersCount' => Matter::where('status', '!=', 'closed')->count(),
            'recentDocuments' => Document::with('matter')->latest()->limit(5)->get(),
            'upcomingTasks' => Task::with('matter')
                ->where('status', '!=', 'done')
                ->whereNotNull('due_date')
                ->orderBy('due_date')
                ->limit(5)
                ->get(),
            'recentActivity' => ActivityLog::with('matter')->latest()->limit(10)->get(),
        ]);
    }

    /**
     * Route: GET /search
     * Basic search across clients, matters, and documents per spec
     * Section 6 ("Search" module) and Section 15's /search contract.
     */
    public function search(Request $request): View
    {
        $term = $request->query('q');

        return view('search.results', [
            'term' => $term,
            'clients' => $term ? Client::where('name', 'ilike', "%{$term}%")->limit(10)->get() : collect(),
            'matters' => $term ? Matter::where('title', 'ilike', "%{$term}%")->limit(10)->get() : collect(),
            'documents' => $term ? Document::where('original_name', 'ilike', "%{$term}%")->limit(10)->get() : collect(),
        ]);
    }
}
