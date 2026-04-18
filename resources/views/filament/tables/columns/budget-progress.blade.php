@php
    $percent = $getRecord()->percentUsed();
    $color = $percent > 100 ? 'rgb(var(--color-danger-600))' : ($percent > 85 ? 'rgb(var(--color-warning-600))' : 'rgb(var(--color-success-600))');
    $bgColor = $percent > 100 ? 'rgb(var(--color-danger-50))' : ($percent > 85 ? 'rgb(var(--color-warning-50))' : 'rgb(var(--color-success-50))');
@endphp

<div class="px-4 py-2 min-w-[180px]">
    <div class="flex items-center justify-between mb-1.5">
        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Usage</span>
        <span class="text-[11px] font-bold" style="color: {{ $color }}">{{ $percent }}%</span>
    </div>
    <div class="h-1.5 w-full rounded-full overflow-hidden" style="background-color: {{ $bgColor }}">
        <div class="h-full transition-all duration-700 ease-out rounded-full" 
             style="width: {{ min(100, $percent) }}%; background-color: {{ $color }}">
        </div>
    </div>
</div>
