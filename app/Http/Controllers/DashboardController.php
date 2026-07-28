<?php

namespace App\Http\Controllers;

use App\Models\Matter;
use App\Models\Document;
use App\Models\Task;
use App\Models\ActivityLog;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'openMattersCount'    => Matter::where('status', '!=', 'closed')->count(),
            'totalMattersCount'   => Matter::count(),
            'pendingTasksCount'   => Task::where('status', '!=', 'done')->count(),
            'totalDocumentsCount' => Document::count(),
            'recentDocuments'     => Document::with('matter')->latest()->limit(5)->get(),
            'upcomingTasks'       => Task::with('matter')
                ->where('status', '!=', 'done')
                ->whereNotNull('due_date')
                ->orderBy('due_date')
                ->limit(5)
                ->get(),
            'recentActivity' => ActivityLog::with('matter')->latest()->limit(10)->get(),
        ]);
    }
}
