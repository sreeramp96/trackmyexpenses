@props(['active' => false])
<a {{ $attributes->merge(['class' => 'flex items-center gap-2.5 px-5 py-2.5 text-sm transition-all duration-100 border-l-2 ' . ($active ? 'text-accent bg-accent/5 border-accent' : 'text-muted border-transparent hover:text-primary hover:bg-surface-3')]) }}>
    <span class="w-3.5 h-3.5 shrink-0 opacity-70 [.active_&]:opacity-100">{{ $icon }}</span>
    {{ $slot }}
</a>
