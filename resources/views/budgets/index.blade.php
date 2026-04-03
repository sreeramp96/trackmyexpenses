<x-app-layout>
    <x-slot name="heading">Budgets</x-slot>
    <x-slot name="actions">
        <button class="flex items-center gap-1.5 text-xs bg-ink text-white px-3 py-1.5 rounded hover:bg-ink-2 transition-colors font-medium">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 12 12"
                 stroke-linecap="round">
                <path d="M6 1v10M1 6h10"/>
            </svg>
            Set Budget
        </button>
    </x-slot>

    <div class="p-5 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($budgets as $b)
                <div class="bg-surface border border-edge rounded overflow-hidden shadow-sm">
                    <div class="p-4 border-b border-edge bg-surface-2 flex justify-between items-center">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3">Budget: {{ $b['category'] }}</span>
                        <x-badge type="{{ $b['is_over_budget'] ? 'red' : ($b['percent_used'] > 85 ? 'amber' : 'green') }}">
                            {{ $b['percent_used'] }}%
                        </x-badge>
                    </div>
                    <div class="p-5">
                         <div class="flex justify-between items-end mb-4">
                            <div>
                                <p class="text-[10px] font-mono font-bold uppercase text-ink-3 opacity-60">Spent</p>
                                <p class="text-xl font-mono font-medium {{ $b['is_over_budget'] ? 'text-finance-red' : 'text-finance-green' }}">₹{{ number_format($b['spent_amount'], 0) }}</p>
                            </div>
                             <div class="text-right">
                                <p class="text-[10px] font-mono font-bold uppercase text-ink-3 opacity-60">Limit</p>
                                <p class="text-xl font-mono font-medium text-ink-2">₹{{ number_format($b['budgeted_amount'], 0) }}</p>
                            </div>
                        </div>

                        <div class="h-2 bg-surface-3 rounded-full overflow-hidden mb-4">
                            <div class="h-full transition-all duration-500 rounded-full"
                                 style="width: {{ min(100,$b['percent_used']) }}%; background: {{ $b['is_over_budget'] ? '#991b1b' : ($b['percent_used'] > 85 ? '#92400e' : '#166534') }}">
                            </div>
                        </div>

                        <div class="flex justify-between items-center text-[10px] font-mono text-ink-3 uppercase">
                            <span>Remaining: ₹{{ number_format(max(0, $b['budgeted_amount'] - $b['spent_amount']), 0) }}</span>
                             <button class="text-finance-blue hover:underline">Edit Budget</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
