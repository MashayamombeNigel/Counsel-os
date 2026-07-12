<x-app-layout>

    <header class="mb-stack-lg flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-headline-xl text-primary">Matters</h1>
            <p class="text-body-md text-on-surface-variant mt-1">
                {{ $matters->total() }} {{ Str::plural('matter', $matters->total()) }}
                @if ($currentStatus || $currentClientId || $currentSearch) matching your filters @endif
            </p>
        </div>
        <a href="{{ route('matters.create') }}"
           class="bg-primary text-white px-4 py-2 rounded-lg text-label-md flex items-center gap-2 hover:bg-primary-container transition-colors shadow-sm">
            <span class="material-symbols-outlined text-lg">add</span>
            New Matter
        </a>
    </header>

    {{-- Filters --}}
    <form method="GET" action="{{ route('matters.index') }}"
          class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-stack-md mb-stack-lg flex flex-col md:flex-row gap-4">

        <div class="relative flex-grow">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
            <input type="text" name="search" value="{{ $currentSearch }}" aria-label="Search matters"
                   placeholder="Search matter titles..."
                   class="w-full bg-surface-container-low border-none rounded-lg pl-10 pr-4 py-2 text-body-sm focus:ring-2 focus:ring-secondary/20">
        </div>

        <select name="status" class="bg-surface-container-low border-none rounded-lg px-3 py-2 text-body-sm focus:ring-2 focus:ring-secondary/20">
            <option value="">All statuses</option>
            @foreach (['open' => 'Open', 'in_review' => 'In Review', 'waiting_client' => 'Waiting Client', 'closed' => 'Closed'] as $value => $label)
                <option value="{{ $value }}" @selected($currentStatus === $value)>{{ $label }}</option>
            @endforeach
        </select>

        <select name="client" aria-label="Filter matters by client" class="bg-surface-container-low border-none rounded-lg px-3 py-2 text-body-sm focus:ring-2 focus:ring-secondary/20">
            <option value="">All clients</option>
            @foreach ($clients as $client)
                <option value="{{ $client->id }}" @selected((string) $currentClientId === (string) $client->id)>{{ $client->name }}</option>
            @endforeach
        </select>

        <button type="submit"
                class="bg-surface-container-high text-on-surface px-4 py-2 rounded-lg text-label-md hover:bg-surface-variant transition-colors">
            Filter
        </button>

        @if ($currentStatus || $currentClientId || $currentSearch)
            <a href="{{ route('matters.index') }}"
               class="text-body-sm text-on-surface-variant hover:text-primary self-center underline">
                Clear
            </a>
        @endif
    </form>

    {{-- Results --}}
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
        @if ($matters->isEmpty())
            <div class="p-12 text-center">
                <span class="material-symbols-outlined text-4xl text-outline-variant mb-2 block">gavel</span>
                <p class="text-body-md text-on-surface-variant">
                    @if ($currentStatus || $currentClientId || $currentSearch)
                        No matters match your filters.
                    @else
                        No matters yet. Create your first matter to get started.
                    @endif
                </p>
            </div>
        @else
            <div class="divide-y divide-surface-variant">
                @foreach ($matters as $matter)
                    <a href="{{ route('matters.show', $matter) }}"
                       class="p-stack-md flex items-center justify-between gap-4 hover:bg-surface-bright transition-colors">
                        <div class="min-w-0">
                            <div class="text-label-md text-primary truncate">{{ $matter->title }}</div>
                            <div class="text-body-sm text-on-surface-variant mt-0.5">
                                {{ $matter->client->name }}
                                @if ($matter->practice_area)
                                    · {{ $matter->practice_area }}
                                @endif
                            </div>
                        </div>
                        <x-matter-status-badge :status="$matter->status" class="flex-shrink-0" />
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <div class="mt-stack-md">
        {{ $matters->links() }}
    </div>
</x-app-layout>