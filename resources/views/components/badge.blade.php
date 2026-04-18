@props(['color' => 'gray'])

@php
    $classes = match($color) {
        'green', 'success' => 'bg-finance-green-bg text-finance-green border-finance-green-border',
        'red', 'danger' => 'bg-finance-red-bg text-finance-red border-finance-red-border',
        'amber', 'warning' => 'bg-finance-amber-bg text-finance-amber border-finance-amber-border',
        'blue', 'info' => 'bg-finance-blue-bg text-finance-blue border-finance-blue-border',
        default => 'bg-surface-3 text-ink-2 border-edge-2',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2 py-0.5 rounded-full text-[0.6rem] font-black uppercase tracking-widest border $classes font-sans"]) }}>
    {{ $slot }}
</span>
