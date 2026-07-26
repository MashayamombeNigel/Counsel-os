<?php

namespace App\Services;

use App\Models\Matter;
use App\Models\Client;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MatterService
{
    public function __construct(
        protected TimelineService $timeline,
    ) {}

    public function search(?string $status = null, ?int $clientId = null, ?string $term = null): LengthAwarePaginator
    {
        return Matter::query()
            ->with('client')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($clientId, fn ($q) => $q->where('client_id', $clientId))
            ->when($term, fn ($q) => $q->where('title', 'ilike', "%{$term}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }

    public function create(Client $client, array $data): Matter
    {
        $matter = Matter::create([
            ...$data,
            'client_id' => $client->id,
            'opened_at' => now(),
        ]);

        $this->timeline->recordMatterEvent(
            matter: $matter,
            action: 'matter_created',
            description: "Matter \"{$matter->title}\" created for {$client->name}.",
        );

        return $matter;
    }

    /**
     * When status changes, stamps closed_at if transitioning to 'closed' and
     * writes an activity log entry. The controller does not need to know this happened.
     */
    public function update(Matter $matter, array $data): Matter
    {
        $previousStatus = $matter->status;

        $matter->update($data);

        if ($previousStatus !== $matter->status) {
            $this->timeline->recordMatterEvent(
                matter: $matter,
                action: 'status_changed',
                description: "Status changed from {$previousStatus} to {$matter->status}.",
            );

            if ($matter->status === 'closed' && ! $matter->closed_at) {
                $matter->update(['closed_at' => now()]);
            }
        }

        return $matter;
    }

    public function getWorkspaceData(Matter $matter): array
    {
        $matter->load([
            'client',
            'documents' => fn ($q) => $q->latest(),
            'documents.documentInsight',
            'tasks' => fn ($q) => $q->orderBy('due_date'),
            'researchSessions' => fn ($q) => $q->latest(),
            'activityLogs' => fn ($q) => $q->latest()->limit(20),
        ]);

        return [
            'matter' => $matter,
            'documents' => $matter->documents,
            'tasks' => $matter->tasks,
            'researchSessions' => $matter->researchSessions,
            'activity' => $matter->activityLogs,
        ];
    }
}
