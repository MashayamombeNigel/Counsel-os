<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Matter;
use App\Http\Requests\StoreMatterRequest;
use App\Http\Requests\UpdateMatterRequest;
use App\Services\MatterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatterController extends Controller
{
    public function __construct(
        protected MatterService $matters,
    ) {}

    public function index(Request $request): View
    {
        return view('matters.index', [
            'matters' => $this->matters->search(
                status: $request->query('status'),
                clientId: $request->query('client'),
                term: $request->query('search'),
            ),
            'clients' => Client::whereNull('archived_at')->orderBy('name')->get(['id', 'name']),
            'currentStatus' => $request->query('status'),
            'currentClientId' => $request->query('client'),
            'currentSearch' => $request->query('search'),
        ]);
    }

    /**
     * Pre-selects the client when arriving from a client profile via ?client_id=,
     * otherwise falls back to a full client picker.
     */
    public function create(Request $request): View
    {
        return view('matters.create', [
            'preselectedClientId' => $request->query('client_id'),
            'clients' => Client::whereNull('archived_at')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreMatterRequest $request): RedirectResponse
    {
        $client = Client::findOrFail($request->validated('client_id'));

        $matter = $this->matters->create($client, $request->safe()->except('client_id'));

        return redirect()
            ->route('matters.show', $matter)
            ->with('status', 'Matter created.');
    }

    public function show(Matter $matter): View
    {
        return view('matters.show', $this->matters->getWorkspaceData($matter));
    }

    public function edit(Matter $matter): View
    {
        return view('matters.edit', ['matter' => $matter]);
    }

    public function update(UpdateMatterRequest $request, Matter $matter): RedirectResponse
    {
        $this->matters->update($matter, $request->validated());

        return redirect()
            ->route('matters.show', $matter)
            ->with('status', 'Matter updated.');
    }
}
