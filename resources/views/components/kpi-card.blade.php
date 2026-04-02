@props(['label', 'value', 'sub' => null, 'color' => 'default', 'icon' => null])
@php
    $styles = [
        'green' => [
            'bg' => 'bg-finance-green-bg',
            'border' => 'border-finance-green-border',
            'text' => 'text-finance-green',
            'sub' => 'text-finance-green/70'
        ],
        'red' => [
            'bg' => 'bg-finance-red-bg',
            'border' => 'border-finance-red-border',
            'text' => 'text-finance-red',
            'sub' => 'text-finance-red/70'
        ],
        'amber' => [
            'bg' => 'bg-finance-amber-bg',
            'border' => 'border-finance-amber-border',
            'text' => 'text-finance-amber',
            'sub' => 'text-finance-amber/70'
        ],
        'blue' => [
            'bg' => 'bg-finance-blue-bg',
            'border' => 'border-finance-blue-border',
            'text' => 'text-finance-blue',
            'sub' => 'text-finance-blue/70'
        ],
        'default' => [
            'bg' => 'bg-surface',
            'border' => 'border-edge',
            'text' => 'text-ink',
            'sub' => 'text-ink-3'
        ],
    ];
    $s = $styles[$color] ?? $styles['default'];
@endphp

<div class="{{ $s['bg'] }} p-4 transition-all hover:brightness-[0.98]">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-[10px] font-mono font-bold uppercase tracking-widest mb-1 opacity-60 {{ $s['text'] }}">{{ $label }}</p>
            <p class="text-2xl font-mono font-medium tracking-tight {{ $s['text'] }}">{{ $value }}</p>
        </div>
        @if($icon)
            <div class="p-1.5 rounded-full {{ $s['text'] }} opacity-20">
                {{ $icon }}
            </div>
        @endif
    </div>
    @if($sub)
        <p class="text-[10px] font-mono mt-2 {{ $s['sub'] }} font-medium">{{ $sub }}</p>
    @endif
</div>
