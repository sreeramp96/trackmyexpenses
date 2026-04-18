@props([
    'label',
    'value',
    'icon' => null,
    'trend' => null,
    'trendColor' => 'text-ink-2'
])

<div {{ $attributes->merge(['class' => 'bg-surface border border-edge rounded-xl p-6']) }}>
    <div class="flex items-center justify-between mb-2">
        <span class="text-[0.65rem] font-black uppercase tracking-widest text-ink-3 font-sans">{{ $label }}</span>
        @if($icon)
            <x-dynamic-component :component="$icon" class="w-4 h-4 text-ink-3" />
        @endif
    </div>
    
    <div class="flex items-baseline gap-2">
        <span class="text-2xl font-black tracking-tight text-ink font-mono italic">
            {{ $value }}
        </span>
        @if($trend)
            <span class="text-[0.65rem] font-black font-mono {{ $trendColor }}">
                {{ $trend }}
            </span>
        @endif
    </div>
</div>
