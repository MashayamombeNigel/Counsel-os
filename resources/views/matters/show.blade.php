<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $matter->title }}</h2>
                <p class="text-sm text-gray-500">{{ $matter->client->name }}</p>
            </div>
            <div class="flex items-center gap-3">
                <x-matter-status-badge :status="$matter->status" />
                <a href="{{ route('matters.edit', $matter) }}" class="text-sm text-gray-600 hover:text-gray-900">Edit</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8">

        {{-- Tab nav - Alpine-driven, no page reload between tabs.
             Query param ?tab= is read once on load so links from
             elsewhere (e.g. dashboard "Research" links) can deep-link. --}}
        <div x-data="{ tab: '{{ request()->query('tab', 'overview') }}' }">
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex gap-6">
                    @foreach ([
                        'overview' => 'Overview',
                        'documents' => 'Documents',
                        'insights' => 'AI Insights',
                        'research' => 'Research',
                        'tasks' => 'Tasks',
                        'timeline' => 'Timeline',
                    ] as $key => $label)
                        <button @click="tab = '{{ $key }}'"
                                :class="tab === '{{ $key }}' ? 'border-gray-900 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                class="whitespace-nowrap py-3 px-1 border-b-2 text-sm font-medium">
                            {{ $label }}
                        </button>
                    @endforeach
                </nav>
            </div>

            {{-- Overview --}}
            <div x-show="tab === 'overview'" class="grid grid-cols-3 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-xs text-gray-500 uppercase">Documents</p>
                    <p class="text-2xl font-semibold">{{ $documents->count() }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-xs text-gray-500 uppercase">Open Tasks</p>
                    <p class="text-2xl font-semibold">{{ $tasks->where('status', '!=', 'done')->count() }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-xs text-gray-500 uppercase">Practice Area</p>
                    <p class="text-lg font-medium">{{ $matter->practice_area ?? '—' }}</p>
                </div>
                @if ($matter->description)
                    <div class="col-span-3 bg-white shadow-sm rounded-lg p-5">
                        <p class="text-xs text-gray-500 uppercase mb-1">Description</p>
                        <p class="text-sm text-gray-700">{{ $matter->description }}</p>
                    </div>
                @endif
            </div>

            {{-- Documents - upload form + list; wired fully in Epic 3 --}}
            <div x-show="tab === 'documents'" x-cloak class="bg-white shadow-sm rounded-lg p-5">
                @if ($documents->isEmpty())
                    <p class="text-sm text-gray-500">No documents uploaded yet.</p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach ($documents as $document)
                            <li class="py-3 flex justify-between items-center text-sm">
                                <a href="{{ route('documents.show', $document) }}" class="font-medium text-gray-900">
                                    {{ $document->original_name }}
                                </a>
                                <x-document-processing-badge :status="$document->processing_status" />
                            </li>
                        @endforeach
                    </ul>
                @endif
                {{-- Upload form arrives in Epic 3 --}}
            </div>

            {{-- AI Insights - populated once documents are analyzed (Epic 4) --}}
            <div x-show="tab === 'insights'" x-cloak class="bg-white shadow-sm rounded-lg p-5">
                <p class="text-sm text-gray-500">AI insights will appear here once a document has been analyzed.</p>
            </div>

            {{-- Research - form wired in Epic 5, ResearchController already exists --}}
            <div x-show="tab === 'research'" x-cloak class="bg-white shadow-sm rounded-lg p-5 space-y-4">
                @if ($researchSessions->isEmpty())
                    <p class="text-sm text-gray-500">No research questions asked yet.</p>
                @else
                    @foreach ($researchSessions as $session)
                        <div class="border rounded-md p-4">
                            <p class="text-sm font-medium text-gray-900">{{ $session->query }}</p>
                            <p class="text-sm text-gray-600 mt-2 whitespace-pre-line">{{ $session->response }}</p>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Tasks - form wired in Epic 5 --}}
            <div x-show="tab === 'tasks'" x-cloak class="bg-white shadow-sm rounded-lg p-5">
                @if ($tasks->isEmpty())
                    <p class="text-sm text-gray-500">No tasks yet.</p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach ($tasks as $task)
                            <li class="py-3 flex justify-between items-center text-sm">
                                <span class="{{ $task->status === 'done' ? 'line-through text-gray-400' : 'text-gray-900' }}">
                                    {{ $task->title }}
                                </span>
                                <span class="text-gray-500">{{ $task->due_date?->format('M j, Y') ?? 'No due date' }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Timeline --}}
            <div x-show="tab === 'timeline'" x-cloak class="bg-white shadow-sm rounded-lg p-5">
                @if ($activity->isEmpty())
                    <p class="text-sm text-gray-500">No activity recorded yet.</p>
                @else
                    <ul class="space-y-3">
                        @foreach ($activity as $entry)
                            <li class="text-sm">
                                <span class="text-gray-400">{{ $entry->created_at->format('M j, g:i A') }}</span>
                                — {{ $entry->description }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
