<x-app-layout>
    {{-- No header slot used here - this page builds its own hero
         section below to match the Executive Overview layout, which
         is more custom than the simple title bar other pages use. --}}

    <header class="mb-stack-lg flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-headline-xl text-primary">Dashboard</h1>
            <p class="text-body-md text-on-surface-variant mt-1">
                Welcome back, {{ auth()->user()->name }}.
                @if ($pendingTasksCount > 0)
                    You have {{ $pendingTasksCount }} pending {{ Str::plural('task', $pendingTasksCount) }} across your matters.
                @else
                    No pending tasks right now.
                @endif
            </p>
        </div>
        <div class="flex gap-2">
            <span class="bg-surface-container-lowest border border-outline-variant px-4 py-2 rounded-lg text-label-md text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">calendar_today</span>
                {{ now()->format('M j, Y') }}
            </span>
            <a href="{{ route('matters.create') }}"
               class="bg-primary text-white px-4 py-2 rounded-lg text-label-md flex items-center gap-2 hover:bg-primary-container transition-colors shadow-sm">
                <span class="material-symbols-outlined text-lg">add</span>
                New Matter
            </a>
        </div>
    </header>

    {{-- Stat cards --}}
    <section class="grid grid-cols-1 md:grid-cols-3 gap-gutter mb-stack-lg">

        <div class="bg-surface-container-lowest p-stack-lg rounded-xl border border-outline-variant shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-secondary/10 rounded-lg flex items-center justify-center text-secondary">
                    <span class="material-symbols-outlined text-3xl">gavel</span>
                </div>
            </div>
            <div class="text-headline-lg text-primary">{{ $openMattersCount }}</div>
            <div class="text-label-md text-on-surface-variant">Open Matters</div>
            @if ($totalMattersCount > 0)
                <div class="w-full bg-surface-container-high h-1.5 rounded-full mt-4 overflow-hidden">
                    <div class="bg-secondary h-full" style="width: {{ round(($openMattersCount / $totalMattersCount) * 100) }}%"></div>
                </div>
            @endif
        </div>

        <div class="bg-surface-container-lowest p-stack-lg rounded-xl border border-outline-variant shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-amber-50 rounded-lg flex items-center justify-center text-amber-600">
                    <span class="material-symbols-outlined text-3xl">assignment_late</span>
                </div>
                @if ($pendingTasksCount > 0)
                    <span class="bg-amber-50 text-amber-700 text-[10px] font-bold px-2 py-1 rounded-full">Pending</span>
                @endif
            </div>
            <div class="text-headline-lg text-primary">{{ $pendingTasksCount }}</div>
            <div class="text-label-md text-on-surface-variant">Pending Tasks</div>
        </div>

        <div class="bg-surface-container-lowest p-stack-lg rounded-xl border border-outline-variant shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-primary/5 rounded-lg flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-3xl">description</span>
                </div>
            </div>
            <div class="text-headline-lg text-primary">{{ $totalDocumentsCount }}</div>
            <div class="text-label-md text-on-surface-variant">Case Documents</div>
        </div>
    </section>

    {{-- Two column: Upcoming Tasks + Recent Documents --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-gutter mb-stack-lg">

        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden flex flex-col">
            <div class="p-stack-md border-b border-surface-variant flex items-center justify-between bg-surface-container-low/50">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-on-surface-variant">checklist</span>
                    <h2 class="text-body-lg font-bold">Upcoming Tasks</h2>
                </div>
            </div>

            @if ($upcomingTasks->isEmpty())
                <p class="p-stack-md text-body-sm text-on-surface-variant">No upcoming tasks with due dates.</p>
            @else
                <div class="divide-y divide-surface-variant">
                    @foreach ($upcomingTasks as $task)
                        @php
                            $daysUntil = now()->startOfDay()->diffInDays($task->due_date->startOfDay(), false);
                            $dueBadge = match (true) {
                                $daysUntil < 0 => ['label' => 'Overdue', 'class' => 'bg-error-container text-on-error-container'],
                                $daysUntil === 0 => ['label' => 'Due Today', 'class' => 'bg-error-container text-on-error-container'],
                                $daysUntil <= 3 => ['label' => "In {$daysUntil} Days", 'class' => 'bg-amber-100 text-amber-800'],
                                default => ['label' => $task->due_date->format('M j'), 'class' => 'bg-surface-container-high text-on-surface-variant'],
                            };
                        @endphp
                        <a href="{{ route('matters.show', ['matter' => $task->matter, 'tab' => 'tasks']) }}"
                           class="p-stack-md flex items-center gap-4 hover:bg-surface-bright transition-colors">
                            <div class="w-5 h-5 rounded border-2 border-outline flex-shrink-0"></div>
                            <div class="flex-grow">
                                <div class="text-label-md text-primary">{{ $task->title }}</div>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="{{ $dueBadge['class'] }} text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $dueBadge['label'] }}</span>
                                    <span class="text-body-sm text-on-surface-variant">{{ $task->matter->title }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden flex flex-col">
            <div class="p-stack-md border-b border-surface-variant flex items-center justify-between bg-surface-container-low/50">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-on-surface-variant">folder_open</span>
                    <h2 class="text-body-lg font-bold">Recent Documents</h2>
                </div>
            </div>

            @if ($recentDocuments->isEmpty())
                <p class="p-stack-md text-body-sm text-on-surface-variant">No documents uploaded yet.</p>
            @else
                <div class="divide-y divide-surface-variant">
                    @foreach ($recentDocuments as $document)
                        @php
                            $isPdf = $document->mime_type === 'application/pdf';
                        @endphp
                        <a href="{{ route('documents.show', $document) }}"
                           class="p-stack-md flex items-center gap-4 hover:bg-surface-bright transition-colors">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                                        {{ $isPdf ? 'bg-error/10 text-error' : 'bg-secondary/10 text-secondary' }}">
                                <span class="material-symbols-outlined">{{ $isPdf ? 'picture_as_pdf' : 'description' }}</span>
                            </div>
                            <div class="flex-grow min-w-0">
                                <div class="text-label-md text-primary truncate">{{ $document->original_name }}</div>
                                <div class="text-body-sm text-on-surface-variant mt-0.5">{{ $document->matter->title }} · {{ number_format($document->file_size / 1024, 0) }} KB</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    {{-- Activity timeline --}}
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden mb-12">
        <div class="p-stack-md border-b border-surface-variant bg-surface-container-low/50">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-on-surface-variant">history</span>
                <h2 class="text-body-lg font-bold">Recent Activity</h2>
            </div>
        </div>

        <div class="p-stack-lg relative">
            @if ($recentActivity->isEmpty())
                <p class="text-body-sm text-on-surface-variant">No activity recorded yet.</p>
            @else
                <div class="absolute left-[2.4rem] top-stack-lg bottom-stack-lg w-0.5 bg-surface-variant"></div>
                <div class="space-y-8 relative">
                    @foreach ($recentActivity as $entry)
                        @php
                            $iconMap = [
                                'matter_created' => ['icon' => 'gavel', 'bg' => 'bg-secondary'],
                                'status_changed' => ['icon' => 'sync', 'bg' => 'bg-secondary'],
                                'document_uploaded' => ['icon' => 'upload_file', 'bg' => 'bg-secondary'],
                                'text_extracted' => ['icon' => 'article', 'bg' => 'bg-secondary'],
                                'ai_analysis_completed' => ['icon' => 'check_circle', 'bg' => 'bg-emerald-500'],
                                'ai_analysis_failed' => ['icon' => 'error', 'bg' => 'bg-red-500'],
                                'extraction_failed' => ['icon' => 'error', 'bg' => 'bg-red-500'],
                                'task_created' => ['icon' => 'assignment', 'bg' => 'bg-amber-500'],
                                'task_status_changed' => ['icon' => 'task_alt', 'bg' => 'bg-amber-500'],
                            ];
                            $iconConfig = $iconMap[$entry->action] ?? ['icon' => 'circle', 'bg' => 'bg-secondary'];
                        @endphp
                        <div class="flex gap-6 items-start">
                            <div class="z-10 w-10 h-10 rounded-full {{ $iconConfig['bg'] }} text-white flex items-center justify-center shadow-md ring-4 ring-white flex-shrink-0">
                                <span class="material-symbols-outlined text-lg">{{ $iconConfig['icon'] }}</span>
                            </div>
                            <div class="flex-grow bg-surface p-4 rounded-xl border border-surface-variant">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-label-md text-primary">{{ $entry->matter->title ?? 'General' }}</span>
                                    <span class="text-body-sm text-on-surface-variant">{{ $entry->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-body-sm text-on-surface-variant">{{ $entry->description }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
