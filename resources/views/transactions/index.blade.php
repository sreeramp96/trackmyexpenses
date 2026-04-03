<x-app-layout>
    <x-slot name="heading">Transactions</x-slot>
    <x-slot name="actions">
        <button onclick="Livewire.dispatch('open-tx-modal')"
                class="flex items-center gap-1.5 text-xs bg-ink text-white px-3 py-1.5 rounded hover:bg-ink-2 transition-colors font-medium">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 12 12"
                 stroke-linecap="round">
                <path d="M6 1v10M1 6h10"/>
            </svg>
            New transaction
        </button>
    </x-slot>

    <div class="p-5 space-y-4">
        <x-panel title="All Transactions">
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse min-w-[800px]">
                    <thead class="bg-surface-2">
                    <tr>
                        <th class="px-3.5 py-2 text-left text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">Date</th>
                        <th class="px-3.5 py-2 text-left text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">Description</th>
                        <th class="px-3.5 py-2 text-left text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">Category</th>
                        <th class="px-3.5 py-2 text-left text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">Account</th>
                        <th class="px-3.5 py-2 text-right text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">Amount</th>
                        <th class="px-3.5 py-2 text-center text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($transactions as $tx)
                        <tr class="border-b border-edge last:border-0 hover:bg-surface-2 transition-colors cursor-pointer"
                            onclick="Livewire.dispatch('open-tx-modal', {id: {{ $tx->id }}})">
                            <td class="px-3.5 py-3 font-mono text-ink-2">{{ $tx->transaction_date->format('M d, Y') }}</td>
                            <td class="px-3.5 py-3 font-medium">{{ $tx->note ?? '—' }}</td>
                            <td class="px-3.5 py-3">
                                @if($tx->category)
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full" style="background-color: {{ $tx->category->color }}"></span>
                                        {{ $tx->category->name }}
                                    </span>
                                @else
                                    <span class="text-ink-3 italic">Uncategorized</span>
                                @endif
                            </td>
                            <td class="px-3.5 py-3 text-ink-2">{{ $tx->account->name }}</td>
                            <td class="px-3.5 py-3 text-right font-mono font-medium {{ $tx->type === 'income' ? 'text-finance-green' : ($tx->type === 'transfer' ? 'text-finance-blue' : 'text-finance-red') }}">
                                {{ $tx->type === 'expense' ? '−' : '+' }}₹{{ number_format($tx->amount, 2) }}
                            </td>
                            <td class="px-3.5 py-3 text-center">
                                @if($tx->is_reconciled)
                                    <x-badge type="green">Reconciled</x-badge>
                                @else
                                    <x-badge type="default">Pending</x-badge>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())
                <div class="px-3.5 py-3 border-t border-edge bg-surface-2">
                    {{ $transactions->links() }}
                </div>
            @endif
        </x-panel>
    </div>
</x-app-layout>
