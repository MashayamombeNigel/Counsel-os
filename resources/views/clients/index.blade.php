<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Clients</h2>
            <a href="{{ route('clients.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                + New Client
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">

        <form method="GET" action="{{ route('clients.index') }}" class="mb-4">
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Search by name, organization, or email..."
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
        </form>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            @if ($clients->isEmpty())
                <div class="p-8 text-center text-gray-500">
                    @if ($search)
                        No clients match "{{ $search }}".
                    @else
                        No clients yet. Create your first client to get started.
                    @endif
                </div>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Organization</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($clients as $client)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $client->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $client->organization ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $client->email ?? '—' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('clients.show', $client) }}"
                                       class="text-sm text-gray-700 hover:text-gray-900 font-medium">View →</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="mt-4">
            {{ $clients->links() }}
        </div>
    </div>
</x-app-layout>
