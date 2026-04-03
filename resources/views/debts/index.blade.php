<x-app-layout>
    <x-slot name="heading">Debts</x-slot>
    <x-slot name="actions">
        <button class="flex items-center gap-1.5 text-xs bg-ink text-white px-3 py-1.5 rounded hover:bg-ink-2 transition-colors font-medium">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 12 12"
                 stroke-linecap="round">
                <path d="M6 1v10M1 6h10"/>
            </svg>
            Add Debt
        </button>
    </x-slot>

    <div class="p-5 space-y-4">
        <x-panel title="All Debts">
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse min-w-[700px]">
                    <thead class="bg-surface-2">
                    <tr>
                        <th class="px-3.5 py-2 text-left text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">Contact Name</th>
                        <th class="px-3.5 py-2 text-left text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">Type</th>
                        <th class="px-3.5 py-2 text-left text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">Original Amount</th>
                        <th class="px-3.5 py-2 text-right text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">Remaining</th>
                        <th class="px-3.5 py-2 text-left text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">Due Date</th>
                        <th class="px-3.5 py-2 text-center text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($debts as $debt)
                        <tr class="border-b border-edge last:border-0 hover:bg-surface-2 transition-colors cursor-pointer">
                            <td class="px-3.5 py-3 font-medium">{{ $debt->contact_name }}</td>
                            <td class="px-3.5 py-3 font-mono text-ink-2 uppercase tracking-tighter">{{ $debt->direction }}</td>
                            <td class="px-3.5 py-3 font-mono text-ink-2">₹{{ number_format($debt->amount, 2) }}</td>
                            <td class="px-3.5 py-3 text-right font-mono font-medium {{ $debt->direction === 'lent' ? 'text-finance-green' : 'text-finance-red' }}">
                                ₹{{ number_format($debt->remaining_amount, 2) }}
                            </td>
                            <td class="px-3.5 py-3 font-mono text-ink-2">
                                {{ $debt->due_date ? $debt->due_date->format('M d, Y') : 'No Date' }}
                                @if($debt->isOverdue())
                                    <span class="text-finance-red text-[10px] ml-1 uppercase">Overdue</span>
                                @endif
                            </td>
                            <td class="px-3.5 py-3 text-center">
                                @if($debt->is_settled)
                                    <x-badge type="green">Settled</x-badge>
                                @else
                                    <x-badge type="{{ $debt->isOverdue() ? 'red' : 'amber' }}">
                                        {{ $debt->isOverdue() ? 'Overdue' : 'Active' }}
                                    </x-badge>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </x-panel>
    </div>
</x-app-layout>
