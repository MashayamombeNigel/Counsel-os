<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $document->original_name }}</h2>
                <p class="text-sm text-gray-500">
                    <a href="{{ route('matters.show', ['matter' => $document->matter, 'tab' => 'documents']) }}"
                       class="hover:underline">{{ $document->matter->title }}</a>
                </p>
            </div>
            <x-document-processing-badge :status="$document->processing_status" />
        </div>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session('status'))
            <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-md p-4">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 text-sm rounded-md p-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6 grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">Type:</span> {{ str($document->document_type)->title() }}</div>
            <div><span class="text-gray-500">Size:</span> {{ number_format($document->file_size / 1024, 1) }} KB</div>
            <div><span class="text-gray-500">Uploaded:</span> {{ $document->created_at->format('M j, Y g:i A') }}</div>
            <div><span class="text-gray-500">Mime type:</span> {{ $document->mime_type }}</div>
        </div>

        @if ($document->processing_status === 'failed' && $document->error_message)
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-800">
                <p class="font-medium mb-1">Processing failed</p>
                <p>{{ $document->error_message }}</p>
            </div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-medium text-gray-800">Extracted Text</h3>

                @if ($document->processing_status === 'uploaded')
                    <form method="POST" action="{{ route('documents.extract', $document) }}">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                            Run Extraction
                        </button>
                    </form>
                @elseif ($document->processing_status === 'extracting')
                    <span class="text-sm text-gray-500 italic">Extraction in progress — refresh to check status.</span>
                @elseif ($document->processing_status === 'failed')
                    <form method="POST" action="{{ route('documents.extract', $document) }}">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 bg-red-700 text-white text-sm font-medium rounded-md hover:bg-red-800">
                            Retry Extraction
                        </button>
                    </form>
                @endif
            </div>

            @if ($document->extracted_text)
                <pre class="text-sm text-gray-700 whitespace-pre-wrap max-h-96 overflow-y-auto bg-gray-50 rounded-md p-4">{{ \Illuminate\Support\Str::limit($document->extracted_text, 3000) }}</pre>
            @else
                <p class="text-sm text-gray-500">No text extracted yet.</p>
            @endif
        </div>
    </div>
</x-app-layout>
