@props(['status'])

@php
    $colors = [
        'open' => 'bg-green-100 text-green-800',
        'in_review' => 'bg-yellow-100 text-yellow-800',
        'waiting_client' => 'bg-blue-100 text-blue-800',
        'closed' => 'bg-gray-100 text-gray-600',
    ];
    $label = str($status)->replace('_', ' ')->title();
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . ($colors[$status] ?? 'bg-gray-100 text-gray-600')]) }}>
    {{ $label }}
</span>
