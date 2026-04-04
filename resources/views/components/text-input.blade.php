@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full bg-surface-2 border border-edge-2 rounded-lg px-4 py-2.5 text-sm font-medium text-ink focus:border-ink focus:ring-0 focus:outline-none transition-all placeholder:text-ink-3/50']) }}>
