<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white shadow-sm rounded-lg p-5">
                <p class="text-xs text-gray-500 uppercase">Open Matters</p>
                <p class="text-2xl font-semibold">{{ $openMattersCount }}</p>
            </div>
            <div class="bg-white shadow-sm rounded-lg p-5">
                <p class="text-xs text-gray-500 uppercase">Upcoming Tasks</p>
                <p class="text-2xl font-semibold">{{ $upcomingTasks->count() }}</p>
            </div>
            <div class="bg-white shadow-sm rounded-lg p-5">
                <p class="text-xs text-gray-500 uppercase">Recent Documents</p>
                <p class="text-2xl font-semibold">{{ $recentDocuments->count() }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b bg-gray-50 font-medium text-gray-700 text-sm">Upcoming Tasks</div>
                @if ($upcomingTasks->isEmpty())
                    <p class="p-5 text-sm text-gray-500">No upcoming tasks with due dates.</p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach ($upcomingTasks as $task)
                            <li class="px-5 py-3 flex justify-between items-center text-sm">
                                <a href="{{ route('matters.show', ['matter' => $task->matter, 'tab' => 'tasks']) }}"
                                   class="text-gray-900 font-medium">{{ $task->title }}</a>
                                <span class="text-gray-500">{{ $task->due_date->format('M j') }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-3 border-b bg-gray-50 font-medium text-gray-700 text-sm">Recent Documents</div>
                @if ($recentDocuments->isEmpty())
                    <p class="p-5 text-sm text-gray-500">No documents uploaded yet.</p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach ($recentDocuments as $document)
                            <li class="px-5 py-3 text-sm">
                                <a href="{{ route('documents.show', $document) }}" class="text-gray-900 font-medium">
                                    {{ $document->original_name }}
                                </a>
                                <span class="text-gray-500 block text-xs">{{ $document->matter->title }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-5 py-3 border-b bg-gray-50 font-medium text-gray-700 text-sm">Recent Activity</div>
            @if ($recentActivity->isEmpty())
                <p class="p-5 text-sm text-gray-500">No activity recorded yet.</p>
            @else
                <ul class="divide-y divide-gray-200">
                    @foreach ($recentActivity as $entry)
                        <li class="px-5 py-3 text-sm">
                            <span class="text-gray-400">{{ $entry->created_at->diffForHumans() }}</span>
                            — {{ $entry->description }}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-app-layout>