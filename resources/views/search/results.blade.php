<x-app-layout>

    <header class="mb-stack-lg">
        <h1 class="text-headline-xl text-primary">Search</h1>
        <form method="GET" action="{{ route('search') }}" class="mt-3 relative max-w-xl">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
            <input type="text" name="q" value="{{ $term }}" autofocus
                   placeholder="Search matters, clients, document text..."
                   aria-label="Search matters, clients, document text"
                   class="w-full bg-surface-container-low border-none rounded-lg pl-10 pr-4 py-3 text-body-md focus:ring-2 focus:ring-secondary/20">
        </form>
    </header>

    @if (! $term)
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-12 text-center">
            <span class="material-symbols-outlined text-4xl text-outline-variant mb-2 block">search</span>
            <p class="text-body-md text-on-surface-variant">Search across clients, matters, and the full text of your uploaded documents.</p>
        </div>
    @else
        @php
            $totalResults = $results['clients']->count() + $results['matters']->count() + $results['documents']->count();
        @endphp

        <p class="text-body-sm text-on-surface-variant mb-stack-lg">
            {{ $totalResults }} {{ Str::plural('result', $totalResults) }} for &ldquo;{{ $term }}&rdquo;
        </p>

        @if ($totalResults === 0)
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant p-12 text-center">
                <p class="text-body-md text-on-surface-variant">No matches found. Try a different term.</p>
            </div>
        @endif

        {{-- Documents — full-text matches with highlighted snippets --}}
        @if ($results['documents']->isNotEmpty())
            <section class="mb-stack-lg">
                <h2 class="text-label-md text-on-surface-variant uppercase tracking-wide mb-2">Documents</h2>
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm divide-y divide-surface-variant overflow-hidden">
                    @foreach ($results['documents'] as $document)
                        <a href="{{ route('documents.show', $document) }}" class="block p-stack-md hover:bg-surface-bright transition-colors">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="material-symbols-outlined text-secondary text-lg">description</span>
                                <span class="text-label-md text-primary">{{ $document->original_name }}</span>
                                <span class="text-body-sm text-on-surface-variant">· {{ $document->matter->title }}</span>
                            </div>
                            @if ($document->safe_snippet)
                                <p class="text-body-sm text-on-surface-variant leading-relaxed [&_mark]:bg-amber-200 [&_mark]:text-on-surface [&_mark]:rounded [&_mark]:px-0.5">
                                    {!! $document->safe_snippet !!}
                                </p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Matters --}}
        @if ($results['matters']->isNotEmpty())
            <section class="mb-stack-lg">
                <h2 class="text-label-md text-on-surface-variant uppercase tracking-wide mb-2">Matters</h2>
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm divide-y divide-surface-variant overflow-hidden">
                    @foreach ($results['matters'] as $matter)
                        <a href="{{ route('matters.show', $matter) }}" class="flex items-center justify-between p-stack-md hover:bg-surface-bright transition-colors">
                            <div>
                                <span class="text-label-md text-primary">{{ $matter->title }}</span>
                                <span class="text-body-sm text-on-surface-variant block">{{ $matter->client->name }}</span>
                            </div>
                            <x-matter-status-badge :status="$matter->status" />
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Clients --}}
        @if ($results['clients']->isNotEmpty())
            <section class="mb-stack-lg">
                <h2 class="text-label-md text-on-surface-variant uppercase tracking-wide mb-2">Clients</h2>
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm divide-y divide-surface-variant overflow-hidden">
                    @foreach ($results['clients'] as $client)
                        <a href="{{ route('clients.show', $client) }}" class="block p-stack-md hover:bg-surface-bright transition-colors">
                            <span class="text-label-md text-primary">{{ $client->name }}</span>
                            @if ($client->organization)
                                <span class="text-body-sm text-on-surface-variant block">{{ $client->organization }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    @endif

</x-app-layout>
