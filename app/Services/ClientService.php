<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ClientService
{
    /**
     * Paginated, optionally search-filtered client list.
     * Matches US-B1 acceptance criteria: created client appears in list.
     */
    public function search(?string $term = null): LengthAwarePaginator
    {
        return Client::query()
            ->whereNull('archived_at')
            ->when($term, function ($query, $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'ilike', "%{$term}%")
                        ->orWhere('organization', 'ilike', "%{$term}%")
                        ->orWhere('email', 'ilike', "%{$term}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }

    public function create(array $data): Client
    {
        return Client::create([
            ...$data,
            'user_id' => Auth::id(),
        ]);
    }

    public function update(Client $client, array $data): Client
    {
        $client->update($data);

        return $client;
    }

    public function archive(Client $client): void
    {
        $client->update(['archived_at' => now()]);
    }

    /**
     * Client profile view data: the client plus its matters and the
     * most recent documents across those matters (US-B1 / dashboard use).
     */
    public function getProfileData(Client $client): array
    {
        $client->load(['matters' => fn ($q) => $q->latest()]);

        $recentDocuments = \App\Models\Document::query()
            ->whereIn('matter_id', $client->matters->pluck('id'))
            ->latest()
            ->limit(5)
            ->get();

        return [
            'client' => $client,
            'matters' => $client->matters,
            'recentDocuments' => $recentDocuments,
        ];
    }
}
