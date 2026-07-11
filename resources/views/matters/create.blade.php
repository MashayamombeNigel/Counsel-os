<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">New Matter</h2>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="POST" action="{{ route('matters.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700">Client *</label>
                    <select name="client_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Select a client...</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}"
                                @selected(old('client_id', $preselectedClientId) == $client->id)>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('client_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Title *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           placeholder="e.g. Lease Review - Riverside Office Unit"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Practice Area</label>
                        <input type="text" name="practice_area" value="{{ old('practice_area') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status *</label>
                        <select name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="open" selected>Open</option>
                            <option value="in_review">In Review</option>
                            <option value="waiting_client">Waiting Client</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description') }}</textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ url()->previous() }}" class="text-sm text-gray-600 hover:text-gray-900 self-center">Cancel</a>
                    <button type="submit"
                            class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                        Create Matter
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
