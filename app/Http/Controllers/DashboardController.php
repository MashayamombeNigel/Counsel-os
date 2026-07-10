<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $dashboards = Dashboard::all();

        return view('dashboard');
    }
}
