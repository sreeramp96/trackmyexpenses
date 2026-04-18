@props(['padding' => 'p-6'])

<div {{ $attributes->merge(['class' => "bg-surface border border-edge rounded-xl $padding"]) }}>
    {{ $slot }}
</div>
