@props(['status'])

@php
    $colors = [
        'uploaded' => 'bg-gray-100 text-gray-600',
        'extracting' => 'bg-blue-100 text-blue-800',
        'analysis_pending' => 'bg-yellow-100 text-yellow-800',
        'analyzed' => 'bg-green-100 text-green-800',
        'failed' => 'bg-red-100 text-red-800',
    ];
    $label = str($status)->replace('_', ' ')->title();
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . ($colors[$status] ?? 'bg-gray-100 text-gray-600')]) }}>
    {{ $label }}
</span>
