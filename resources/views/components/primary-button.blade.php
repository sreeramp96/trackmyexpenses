<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-2.5 bg-ink text-white border border-transparent rounded-lg font-bold text-[10px] uppercase tracking-[0.15em] hover:bg-ink-2 focus:outline-none focus:ring-0 transition ease-in-out duration-150 shadow-md active:scale-[0.98]']) }}>
    {{ $slot }}
</button>
