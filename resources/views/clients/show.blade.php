<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $client->name }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('clients.edit', $client) }}"
                   class="text-sm text-gray-600 hover:text-gray-900 self-center">Edit</a>
                <a href="{{ route('matters.create', ['client_id' => $client->id]) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                    + New Matter
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <div class="bg-white shadow-sm rounded-lg p-6 grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">Organization:</span> {{ $client->organization ?? '—' }}</div>
            <div><span class="text-gray-500">Email:</span> {{ $client->email ?? '—' }}</div>
            <div><span class="text-gray-500">Phone:</span> {{ $client->phone ?? '—' }}</div>
            <div><span class="text-gray-500">Address:</span> {{ $client->address ?? '—' }}</div>
            @if ($client->notes)
                <div class="col-span-2"><span class="text-gray-500">Notes:</span> {{ $client->notes }}</div>
            @endif
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-3 border-b bg-gray-50 font-medium text-gray-700 text-sm">Matters</div>
            @if ($matters->isEmpty())
                <div class="p-6 text-center text-gray-500 text-sm">No matters yet for this client.</div>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($matters as $matter)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    <a href="{{ route('matters.show', $matter) }}">{{ $matter->title }}</a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $matter->practice_area ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <x-matter-status-badge :status="$matter->status" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-3 border-b bg-gray-50 font-medium text-gray-700 text-sm">Recent Documents</div>
            @if ($recentDocuments->isEmpty())
                <div class="p-6 text-center text-gray-500 text-sm">No documents uploaded yet.</div>
            @else
                <ul class="divide-y divide-gray-200">
                    @foreach ($recentDocuments as $document)
                        <li class="px-6 py-3 text-sm">
                            <a href="{{ route('documents.show', $document) }}" class="text-gray-900 font-medium">
                                {{ $document->original_name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-app-layout>
