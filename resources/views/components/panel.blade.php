@props(['title', 'action' => null, 'color' => 'default'])
@php
    $headerStyles = [
        'default' => 'bg-surface-2 border-edge text-ink-2',
        'blue' => 'bg-finance-blue-bg/30 border-finance-blue-border/50 text-finance-blue',
        'green' => 'bg-finance-green-bg/30 border-finance-green-border/50 text-finance-green',
        'red' => 'bg-finance-red-bg/30 border-finance-red-border/50 text-finance-red',
    ];
    $s = $headerStyles[$color] ?? $headerStyles['default'];
@endphp
<div class="bg-surface border border-edge rounded-lg overflow-hidden shadow-sm hover:shadow transition-shadow">
    <div class="flex items-center justify-between px-4 py-2.5 border-b {{ $s }}">
        <span class="text-[10px] font-mono font-bold uppercase tracking-widest">{{ $title }}</span>
        @if($action)
            <div class="flex items-center gap-2">{{ $action }}</div>
        @endif
    </div>
    <div class="p-0">
        {{ $slot }}
    </div>
</div>
