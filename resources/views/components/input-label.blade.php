@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>
