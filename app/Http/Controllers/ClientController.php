<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Services\ClientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function __construct(
        protected ClientService $clients,
    ) {}

    public function index(Request $request): View
    {
        return view('clients.index', [
            'clients' => $this->clients->search($request->query('search')),
            'search' => $request->query('search'),
        ]);
    }

    public function create(): View
    {
        return view('clients.create');
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $client = $this->clients->create($request->validated());

        return redirect()
            ->route('clients.show', $client)
            ->with('status', 'Client created.');
    }

    public function show(Client $client): View
    {
        return view('clients.show', $this->clients->getProfileData($client));
    }

    public function edit(Client $client): View
    {
        return view('clients.edit', ['client' => $client]);
    }

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $this->clients->update($client, $request->validated());

        return redirect()
            ->route('clients.show', $client)
            ->with('status', 'Client updated.');
    }

    public function archive(Client $client): RedirectResponse
    {
        $this->clients->archive($client);

        return redirect()
            ->route('clients.index')
            ->with('status', 'Client archived.');
    }
}
