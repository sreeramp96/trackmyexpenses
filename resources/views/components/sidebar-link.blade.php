@props(['active' => false])
<a {{ $attributes->merge([
  'class' => 'flex items-center gap-3 px-4 py-2 text-xs transition-all duration-200 border-l-4 ' .
    ($active
      ? 'bg-finance-blue/5 text-finance-blue font-semibold border-finance-blue'
      : 'text-ink-2 border-transparent hover:bg-surface-2 hover:text-ink hover:border-edge')
]) }}
:class="collapsed ? 'justify-center border-l-0' : ''"
x-tooltip.placement.right="collapsed ? '{{ $slot }}' : ''"
>
    <span class="w-4 h-4 shrink-0 transition-transform duration-200 {{ $active ? 'text-finance-blue' : 'text-ink-3' }}" :class="collapsed ? 'scale-110' : ''">{{ $icon }}</span>
    <span class="truncate transition-opacity duration-200" :class="collapsed ? 'hidden md:hidden lg:hidden' : 'block'">{{ $slot }}</span>
</a>
