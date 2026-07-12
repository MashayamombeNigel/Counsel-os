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

            {{-- Documents - upload form + list --}}
            <div x-show="tab === 'documents'" x-cloak class="space-y-4">

                <div class="bg-white shadow-sm rounded-lg p-5">
                    <form method="POST" action="{{ route('matters.documents.store', $matter) }}"
                          enctype="multipart/form-data" class="flex flex-wrap gap-3 items-end">
                        @csrf

                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-sm font-medium text-gray-700">File (PDF or DOCX, max 20MB)</label>
                            <input type="file" name="file" accept=".pdf,.docx" required
                                   class="mt-1 block w-full text-sm text-gray-600">
                            @error('file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Document Type</label>
                            <select name="document_type" required
                                    class="mt-1 block rounded-md border-gray-300 shadow-sm text-sm">
                                <option value="contract">Contract</option>
                                <option value="lease">Lease</option>
                                <option value="title_deed">Title Deed</option>
                                <option value="correspondence">Correspondence</option>
                                <option value="research">Research</option>
                                <option value="other">Other</option>
                            </select>
                            @error('document_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit"
                                class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                            Upload
                        </button>
                    </form>
                </div>

                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    @if ($documents->isEmpty())
                        <p class="p-5 text-sm text-gray-500">No documents uploaded yet.</p>
                    @else
                        <ul class="divide-y divide-gray-200">
                            @foreach ($documents as $document)
                                <li class="px-5 py-3 flex justify-between items-center text-sm">
                                    <a href="{{ route('documents.show', $document) }}" class="font-medium text-gray-900">
                                        {{ $document->original_name }}
                                    </a>
                                    <x-document-processing-badge :status="$document->processing_status" />
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- AI Insights - populated once documents are analyzed (Epic 4) --}}
            <div x-show="tab === 'insights'" x-cloak class="bg-white shadow-sm rounded-lg p-5">
                <p class="text-sm text-gray-500">AI insights will appear here once a document has been analyzed.</p>
            </div>

            {{-- Research - form + history --}}
            <div x-show="tab === 'research'" x-cloak class="space-y-4">

                <div class="bg-white shadow-sm rounded-lg p-5">
                    <form method="POST" action="{{ route('matters.research.store', $matter) }}" class="space-y-3">
                        @csrf
                        <label class="block text-sm font-medium text-gray-700">Ask a question about this matter</label>
                        <textarea name="query" rows="2" maxlength="1000" required
                                  placeholder="e.g. What liabilities does the tenant assume?"
                                  class="block w-full rounded-md border-gray-300 shadow-sm">{{ old('query') }}</textarea>
                        @error('query') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                        <button type="submit"
                                class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                            Ask
                        </button>
                        <p class="text-xs text-gray-500">
                            Answers are grounded only in this matter's analyzed documents and are review assistance,
                            not legal advice.
                        </p>
                    </form>
                </div>

                @if ($researchSessions->isEmpty())
                    <p class="text-sm text-gray-500 px-1">No research questions asked yet.</p>
                @else
                    @foreach ($researchSessions as $session)
                        <div class="bg-white shadow-sm rounded-lg p-5">
                            <p class="text-sm font-medium text-gray-900">{{ $session->query }}</p>
                            <p class="text-sm text-gray-600 mt-2 whitespace-pre-line">{{ $session->response }}</p>
                            @if (! empty($session->sources_json))
                                <p class="text-xs text-gray-400 mt-3">
                                    Sources: {{ implode(', ', $session->sources_json) }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Tasks - form + list, with prefill support for
                 "Convert to Task" links coming from the AI Insights
                 deadlines section (?tab=tasks&prefill_title=...&prefill_due_date=...&source_document_id=...) --}}
            <div x-show="tab === 'tasks'" x-cloak class="space-y-4">

                <div class="bg-white shadow-sm rounded-lg p-5">
                    <form method="POST" action="{{ route('matters.tasks.store', $matter) }}" class="space-y-3">
                        @csrf
                        <input type="hidden" name="source_document_id" value="{{ request()->query('source_document_id') }}">

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Title *</label>
                            <input type="text" name="title" required
                                   value="{{ old('title', request()->query('prefill_title')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Due Date</label>
                                <input type="date" name="due_date"
                                       value="{{ old('due_date', request()->query('prefill_due_date')) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Priority *</label>
                                <select name="priority" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" rows="2"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description', request()->query('prefill_reason')) }}</textarea>
                        </div>

                        <button type="submit"
                                class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                            Create Task
                        </button>
                    </form>
                </div>

                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    @if ($tasks->isEmpty())
                        <p class="p-5 text-sm text-gray-500">No tasks yet.</p>
                    @else
                        <ul class="divide-y divide-gray-200">
                            @foreach ($tasks as $task)
                                <li class="px-5 py-3 flex justify-between items-center text-sm">
                                    <div>
                                        <span class="{{ $task->status === 'done' ? 'line-through text-gray-400' : 'text-gray-900 font-medium' }}">
                                            {{ $task->title }}
                                        </span>
                                        <span class="text-gray-500 block text-xs">
                                            {{ $task->due_date?->format('M j, Y') ?? 'No due date' }} · {{ str($task->priority)->title() }} priority
                                        </span>
                                    </div>
                                    @if ($task->status !== 'done')
                                        <form method="POST" action="{{ route('tasks.update', $task) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="done">
                                            <input type="hidden" name="priority" value="{{ $task->priority }}">
                                            <input type="hidden" name="due_date" value="{{ $task->due_date?->format('Y-m-d') }}">
                                            <button type="submit" class="text-xs text-gray-600 hover:text-gray-900 underline">
                                                Mark done
                                            </button>
                                        </form>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
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
