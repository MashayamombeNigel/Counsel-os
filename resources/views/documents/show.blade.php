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

        @if ($insight)
            <div class="bg-white shadow-sm rounded-lg p-6 space-y-6">
                <div class="flex justify-between items-start">
                    <h3 class="font-medium text-gray-800">AI Insights</h3>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-md p-3 text-xs text-amber-800">
                    This is AI-generated review assistance and does not constitute legal advice.
                    All outputs require verification by a qualified legal professional.
                </div>

                <div>
                    <p class="text-xs text-gray-500 uppercase mb-1">Summary</p>
                    <p class="text-sm text-gray-700">{{ $insight->summary }}</p>
                </div>

                @if (! empty($insight->key_parties_json))
                    <div>
                        <p class="text-xs text-gray-500 uppercase mb-1">Key Parties</p>
                        <ul class="list-disc list-inside text-sm text-gray-700">
                            @foreach ($insight->key_parties_json as $party)
                                <li>{{ $party }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (! empty($insight->risks_json))
                    <div>
                        <p class="text-xs text-gray-500 uppercase mb-2">Risks</p>
                        <div class="space-y-2">
                            @foreach ($insight->risks_json as $risk)
                                @php
                                    $severityColors = [
                                        'high' => 'bg-red-50 border-red-200 text-red-800',
                                        'medium' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
                                        'low' => 'bg-gray-50 border-gray-200 text-gray-700',
                                    ];
                                @endphp
                                <div class="border rounded-md p-3 text-sm {{ $severityColors[$risk['severity'] ?? 'low'] ?? $severityColors['low'] }}">
                                    <p class="font-medium">{{ $risk['title'] ?? 'Untitled risk' }}
                                        <span class="text-xs uppercase ml-1">({{ $risk['severity'] ?? 'unknown' }})</span>
                                    </p>
                                    <p class="mt-1">{{ $risk['reason'] ?? '' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (! empty($insight->obligations_json))
                    <div>
                        <p class="text-xs text-gray-500 uppercase mb-2">Obligations</p>
                        <table class="min-w-full text-sm">
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($insight->obligations_json as $obligation)
                                    <tr>
                                        <td class="py-2 pr-4 font-medium text-gray-800">{{ $obligation['party'] ?? '—' }}</td>
                                        <td class="py-2 text-gray-700">{{ $obligation['obligation'] ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if (! empty($insight->deadlines_json))
                    <div>
                        <p class="text-xs text-gray-500 uppercase mb-2">Deadlines</p>
                        <div class="space-y-2">
                            @foreach ($insight->deadlines_json as $deadline)
                                <div class="flex justify-between items-center border rounded-md p-3 text-sm">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $deadline['title'] ?? 'Untitled deadline' }}</p>
                                        <p class="text-gray-500">{{ $deadline['reason'] ?? '' }}</p>
                                    </div>
                                    <span class="text-gray-600">{{ $deadline['date'] ?? 'unknown' }}</span>
                                    {{-- "Convert to Task" wiring arrives in Epic 5 --}}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (! empty($insight->questions_json))
                    <div>
                        <p class="text-xs text-gray-500 uppercase mb-1">Questions for Lawyer</p>
                        <ul class="list-disc list-inside text-sm text-gray-700">
                            @foreach ($insight->questions_json as $question)
                                <li>{{ $question }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
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
                @elseif ($document->processing_status === 'analysis_pending')
                    <form method="POST" action="{{ route('documents.analyze', $document) }}">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                            Run AI Analysis
                        </button>
                    </form>
                @elseif ($document->processing_status === 'analyzed')
                    <span class="text-sm text-green-700 italic">Analysis complete — see AI Insights below.</span>
                @elseif ($document->processing_status === 'failed')
                    @if (! $document->extracted_text)
                        <form method="POST" action="{{ route('documents.extract', $document) }}">
                            @csrf
                            <button type="submit"
                                    class="px-4 py-2 bg-red-700 text-white text-sm font-medium rounded-md hover:bg-red-800">
                                Retry Extraction
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('documents.analyze', $document) }}">
                            @csrf
                            <button type="submit"
                                    class="px-4 py-2 bg-red-700 text-white text-sm font-medium rounded-md hover:bg-red-800">
                                Retry Analysis
                            </button>
                        </form>
                    @endif
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
