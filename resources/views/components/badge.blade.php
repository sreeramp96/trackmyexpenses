@props(['type' => 'default'])
@php
    $map = [
        'green' => 'bg-finance-green-bg text-finance-green border-finance-green-border font-semibold',
        'red' => 'bg-finance-red-bg text-finance-red border-finance-red-border font-semibold',
        'amber' => 'bg-finance-amber-bg text-finance-amber border-finance-amber-border font-semibold',
        'blue' => 'bg-finance-blue-bg text-finance-blue border-finance-blue-border font-semibold',
        'default' => 'bg-surface-3 text-ink-2 border-edge font-medium',
    ];
@endphp
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-mono border {{ $map[$type] ?? $map['default'] }} uppercase tracking-wider transition-all shadow-sm">
    {{ $slot }}
</span>
