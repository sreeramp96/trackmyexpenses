@props(['label', 'value', 'sub' => null, 'color' => 'default', 'icon' => null, 'numeric' => true])
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
    
    // Extract numeric part for animation if it's a currency string or number
    $cleanValue = 0;
    if ($numeric) {
        $cleanValue = (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }
@endphp

<div class="{{ $s['bg'] }} p-4 border border-transparent hover:border-edge transition-all hover:scale-[1.01] hover:shadow-sm group relative overflow-hidden rounded-lg"
     wire:loading.class="opacity-50 grayscale transition-all duration-500">
    
    {{-- Skeleton pulse overlay --}}
    <div wire:loading class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent skeleton-pulse z-10"></div>

    <div class="flex items-start justify-between relative z-0">
        <div>
            <p class="text-[10px] font-mono font-bold uppercase tracking-widest mb-1 opacity-60 {{ $s['text'] }}">{{ $label }}</p>
            
            @if($numeric)
                <div x-data="{ 
                        count: 0, 
                        target: {{ $cleanValue }},
                        duration: 1000,
                        start: null,
                        init() {
                            requestAnimationFrame((timestamp) => this.animate(timestamp));
                        },
                        animate(timestamp) {
                            if (!this.start) this.start = timestamp;
                            const progress = Math.min((timestamp - this.start) / this.duration, 1);
                            this.count = (progress * this.target).toFixed(2);
                            if (progress < 1) {
                                requestAnimationFrame((t) => this.animate(t));
                            }
                        }
                    }" 
                    class="text-2xl font-mono font-medium tracking-tight {{ $s['text'] }}">
                    <span class="opacity-70 text-lg mr-0.5">{{ str_contains($value, '₹') ? '₹' : '' }}</span><span x-text="Number(count).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                </div>
            @else
                <p class="text-2xl font-mono font-medium tracking-tight {{ $s['text'] }}">{{ $value }}</p>
            @endif
        </div>
        @if($icon)
            <div class="p-1.5 rounded-full {{ $s['text'] }} opacity-20 group-hover:opacity-40 transition-opacity">
                {{ $icon }}
            </div>
        @endif
    </div>
    @if($sub)
        <p class="text-[10px] font-mono mt-2 {{ $s['sub'] }} font-medium">{{ $sub }}</p>
    @endif
</div>
