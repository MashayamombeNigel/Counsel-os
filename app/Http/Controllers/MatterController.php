<?php

namespace App\Http\Controllers;

use App\Http\Requests\MatterStoreRequest;
use App\Http\Requests\MatterUpdateRequest;
use App\Models\Matter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatterController extends Controller
{
    public function index(Request $request): Response
    {
        $matters = Matter::all();

        return view('matter.index', [
            'matters' => $matters,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('matter.create');
    }

    public function store(MatterStoreRequest $request): Response
    {
        $matter = Matter::create($request->validated());

        $request->session()->flash('matter.id', $matter->id);

        return redirect()->route('matters.index');
    }

    public function show(Request $request, Matter $matter): Response
    {
        return view('matter.show', [
            'matter' => $matter,
        ]);
    }

    public function edit(Request $request, Matter $matter): Response
    {
        return view('matter.edit', [
            'matter' => $matter,
        ]);
    }

    public function update(MatterUpdateRequest $request, Matter $matter): Response
    {
        $matter->update($request->validated());

        $request->session()->flash('matter.id', $matter->id);

        return redirect()->route('matters.index');
    }

    public function destroy(Request $request, Matter $matter): Response
    {
        $matter->delete();

        return redirect()->route('matters.index');
    }
}
