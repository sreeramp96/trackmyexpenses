<div x-data="{ open: @entangle('open') }">
    {{-- Modal Backdrop --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-ink/40 px-4"
         @click.self="open = false"
         x-cloak>
        
        {{-- Modal Content --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="bg-surface border border-edge-2 rounded-xl w-full max-w-md shadow-2xl overflow-hidden"
             @click.stop>
            
            <div class="flex items-center justify-between px-5 py-4 border-b border-edge">
                <h2 class="text-sm font-bold uppercase tracking-widest text-ink-2">{{ $editingId ? 'Edit' : 'New' }} transaction</h2>
                <button @click="open = false" class="text-ink-3 hover:text-ink text-2xl leading-none transition-colors">
                    ×
                </button>
            </div>

            <div class="p-6 space-y-5">
                {{-- Type Selector (Pills) --}}
                <div class="flex p-1 bg-surface-2 border border-edge rounded-lg">
                    @foreach(['expense' => 'Expense', 'income' => 'Income', 'transfer' => 'Transfer'] as $val => $label)
                        <button type="button" wire:click="$set('type', '{{ $val }}')"
                                class="flex-1 py-2 text-xs font-bold uppercase tracking-tighter rounded-md transition-all
                                {{ $type === $val 
                                    ? ($val === 'income' ? 'bg-finance-green text-white shadow-sm' : ($val === 'expense' ? 'bg-finance-red text-white shadow-sm' : 'bg-finance-blue text-white shadow-sm'))
                                    : 'text-ink-3 hover:text-ink hover:bg-surface-3' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                {{-- Amount with dynamic currency prefix --}}
                <div>
                    <label class="block text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 mb-1.5">Amount</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-2xl font-mono text-ink-3">₹</span>
                        <input wire:model="amount" type="number" step="0.01" placeholder="0.00"
                               class="w-full bg-surface-2 border border-edge-2 rounded-lg pl-10 pr-4 py-3 text-3xl font-mono font-medium text-ink focus:border-ink focus:ring-0 focus:outline-none transition-all">
                    </div>
                    @error('amount') <p class="text-[10px] text-finance-red mt-1 font-mono uppercase">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Account --}}
                    <div>
                        <label class="block text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 mb-1.5">{{ $type === 'transfer' ? 'From' : 'Account' }}</label>
                        <select wire:model="account_id" class="w-full border border-edge-2 rounded-lg px-3 py-2 text-sm bg-surface-2 text-ink focus:border-ink focus:ring-0 focus:outline-none pr-8">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                            @endforeach
                        </select>
                        @error('account_id') <p class="text-[10px] text-finance-red mt-1 font-mono uppercase">{{ $message }}</p> @enderror
                    </div>

                    {{-- To Account (Transfers) or Category --}}
                    @if($type === 'transfer')
                        <div>
                            <label class="block text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 mb-1.5">To</label>
                            <select wire:model="to_account_id" class="w-full border border-edge-2 rounded-lg px-3 py-2 text-sm bg-surface-2 text-ink focus:border-ink focus:ring-0 focus:outline-none pr-8">
                                <option value="">Select Target</option>
                                @foreach($accounts as $acc)
                                    @if($acc->id != $account_id)
                                        <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('to_account_id') <p class="text-[10px] text-finance-red mt-1 font-mono uppercase">{{ $message }}</p> @enderror
                        </div>
                    @else
                        <div>
                            <label class="block text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 mb-1.5">Category</label>
                            <select wire:model="category_id" class="w-full border border-edge-2 rounded-lg px-3 py-2 text-sm bg-surface-2 text-ink focus:border-ink focus:ring-0 focus:outline-none pr-8">
                                <option value="">Uncategorized</option>
                                @foreach($categories->where('type', $type) as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                {{-- Date with Quick Select --}}
                <div>
                    <label class="block text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 mb-1.5">Date</label>
                    <div class="flex gap-2 mb-2 overflow-x-auto pb-1 custom-scrollbar">
                        <button type="button" @click="$wire.set('transaction_date', '{{ now()->toDateString() }}')" 
                                class="shrink-0 px-2 py-1 text-[9px] font-bold uppercase border border-edge rounded bg-surface hover:bg-surface-2 transition-colors">Today</button>
                        <button type="button" @click="$wire.set('transaction_date', '{{ now()->subDay()->toDateString() }}')" 
                                class="shrink-0 px-2 py-1 text-[9px] font-bold uppercase border border-edge rounded bg-surface hover:bg-surface-2 transition-colors">Yesterday</button>
                    </div>
                    <input wire:model="transaction_date" type="date"
                           class="w-full border border-edge-2 rounded-lg px-3 py-2 text-sm bg-surface-2 text-ink focus:border-ink focus:ring-0 focus:outline-none">
                    @error('transaction_date') <p class="text-[10px] text-finance-red mt-1 font-mono uppercase">{{ $message }}</p> @enderror
                </div>

                {{-- Note --}}
                <div>
                    <label class="block text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 mb-1.5">Note</label>
                    <textarea wire:model="note" rows="2" placeholder="What was this for?"
                              class="w-full border border-edge-2 rounded-lg px-3 py-2 text-sm bg-surface-2 text-ink focus:border-ink focus:ring-0 focus:outline-none resize-none"></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 px-6 py-4 border-t border-edge bg-surface-2">
                <button @click="open = false" type="button"
                        class="text-xs px-4 py-2 border border-edge rounded-lg bg-surface text-ink-2 hover:bg-surface-3 transition-colors font-medium">
                    Cancel
                </button>
                <button wire:click="save" type="button" wire:loading.attr="disabled"
                        class="text-xs px-6 py-2 bg-ink text-white rounded-lg hover:bg-ink-2 transition-colors font-bold uppercase tracking-widest relative">
                    <span wire:loading.remove>{{ $editingId ? 'Update' : 'Save' }}</span>
                    <span wire:loading class="flex items-center gap-2">
                        <svg class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Saving...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
