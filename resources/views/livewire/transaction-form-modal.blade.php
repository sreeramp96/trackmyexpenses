<div>
    @if($open)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-ink/40" wire:click.self="$set('open',false)">
            <div class="bg-surface border border-edge-2 rounded w-full max-w-md shadow-xl" @click.stop>

                <div class="flex items-center justify-between px-4 py-3 border-b border-edge">
                    <h2 class="text-sm font-medium">{{ $editingId ? 'Edit' : 'New' }} transaction</h2>
                    <button wire:click="$set('open',false)" class="text-ink-3 hover:text-ink text-lg leading-none px-1">
                        ×
                    </button>
                </div>

                <div class="p-4">

                    {{-- Type selector --}}
                    <div class="flex border border-edge rounded overflow-hidden mb-4">
                        @foreach(['expense'=>'Expense','income'=>'Income','transfer'=>'Transfer'] as $val=>$label)
                            <button wire:click="$set('type','{{ $val }}')"
                                    class="flex-1 py-1.5 text-xs font-medium transition-all border-r border-edge last:border-r-0
              {{ $type===$val
                ? ($val==='income' ? 'bg-finance-green-bg text-finance-green' : ($val==='expense' ? 'bg-finance-red-bg text-finance-red' : 'bg-finance-blue-bg text-finance-blue'))
                : 'bg-surface text-ink-2 hover:bg-surface-2' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Amount --}}
                    <div class="mb-3">
                        <label
                            class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1">Amount
                            (₹)</label>
                        <input wire:model="amount" type="number" step="0.01" placeholder="0.00"
                               class="w-full border border-edge-2 rounded px-3 py-2 text-lg font-mono font-medium bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none">
                        @error('amount')<p class="text-[10px] text-finance-red mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label
                                class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1">From
                                account</label>
                            <select wire:model="account_id"
                                    class="w-full border border-edge-2 rounded px-2.5 py-1.5 text-xs bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none">
                                <option value="">Select...</option>
                                @foreach($accounts as $a)
                                    <option value="{{ $a->id }}">{{ $a->name }}</option>
                                @endforeach
                            </select>
                            @error('account_id')<p class="text-[10px] text-finance-red mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1">
                                {{ $type === 'transfer' ? 'To account' : 'Category' }}
                            </label>
                            @if($type === 'transfer')
                                <select wire:model="to_account_id"
                                        class="w-full border border-edge-2 rounded px-2.5 py-1.5 text-xs bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none">
                                    <option value="">Select...</option>
                                    @foreach($accounts as $a)
                                        <option value="{{ $a->id }}">{{ $a->name }}</option>
                                    @endforeach
                                </select>
                                @error('to_account_id')<p
                                    class="text-[10px] text-finance-red mt-1">{{ $message }}</p>@enderror
                            @else
                                <select wire:model="category_id"
                                        class="w-full border border-edge-2 rounded px-2.5 py-1.5 text-xs bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none">
                                    <option value="">Uncategorized</option>
                                    @foreach($categories->where('type', $type === 'income' ? 'income' : 'expense') as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label
                                class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1">Date</label>
                            <input wire:model="transaction_date" type="date"
                                   class="w-full border border-edge-2 rounded px-2.5 py-1.5 text-xs bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1">Reference
                                #</label>
                            <input wire:model="reference_number" type="text" placeholder="Optional"
                                   class="w-full border border-edge-2 rounded px-2.5 py-1.5 text-xs bg-surface text-ink placeholder:text-ink-3 focus:border-ink focus:ring-0 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1">Note</label>
                        <input wire:model="note" type="text" placeholder="What was this for?"
                               class="w-full border border-edge-2 rounded px-2.5 py-1.5 text-xs bg-surface text-ink placeholder:text-ink-3 focus:border-ink focus:ring-0 focus:outline-none">
                    </div>
                </div>

                <div class="flex justify-end gap-2 px-4 py-3 border-t border-edge bg-surface-2">
                    <button wire:click="$set('open',false)"
                            class="text-xs px-3 py-1.5 border border-edge rounded bg-surface text-ink-2 hover:bg-surface-3 transition-colors">
                        Cancel
                    </button>
                    <button wire:click="save"
                            class="text-xs px-4 py-1.5 bg-ink text-white rounded hover:bg-ink-2 transition-colors font-medium">
                        {{ $editingId ? 'Update' : 'Save transaction' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
