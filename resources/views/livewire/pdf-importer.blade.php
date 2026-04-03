<div class="p-6">
    <x-slot name="heading">Import Bank Statement (PDF)</x-slot>

    <div class="max-w-4xl mx-auto">
        @if($step === 1)
            <x-panel title="Upload HDFC Statement">
                <div class="p-12 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-finance-red-bg rounded-full flex items-center justify-center mb-4 text-finance-red">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-medium mb-2">Upload your PDF statement</h2>
                        <p class="text-sm text-ink-3 mb-6">Currently optimized for HDFC Bank savings account statements.</p>
                        
                        <input type="file" wire:model="file" class="hidden" id="pdf-upload" accept=".pdf">
                        <label for="pdf-upload" class="cursor-pointer bg-ink text-white px-8 py-3 rounded-lg font-bold hover:bg-ink-2 transition-all shadow-md">
                            Select PDF File
                        </label>
                        <div wire:loading wire:target="file" class="mt-4 text-xs text-ink-3">Analyzing statement structure...</div>
                    </div>
                </div>
            </x-panel>
        @endif

        @if($step === 2)
            <div class="space-y-4">
                <x-panel title="Import Settings" color="blue">
                    <div class="p-4 bg-surface-2 border-b border-edge">
                        <div class="max-w-xs">
                            <label class="block text-[10px] font-mono font-bold uppercase text-ink-3 mb-1">Target Account</label>
                            <select wire:model="accountId" class="w-full text-xs border-edge rounded pr-8 py-2">
                                <option value="">Select Account</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                @endforeach
                            </select>
                            @error('accountId') <span class="text-[10px] text-finance-red">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </x-panel>

                <x-panel title="Extracted Transactions & Auto-Categorization" color="green">
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs border-collapse">
                            <thead class="bg-surface-2">
                                <tr>
                                    <th class="px-4 py-2 text-left border-b border-edge">Date</th>
                                    <th class="px-4 py-2 text-left border-b border-edge">Description</th>
                                    <th class="px-4 py-2 text-left border-b border-edge">Category</th>
                                    <th class="px-4 py-2 text-right border-b border-edge">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($previewData as $index => $row)
                                    <tr class="border-b border-edge last:border-0">
                                        <td class="px-4 py-3 font-mono">{{ $row['transaction_date'] }}</td>
                                        <td class="px-4 py-3 truncate max-w-[250px]" title="{{ $row['note'] }}">{{ $row['note'] }}</td>
                                        <td class="px-4 py-3">
                                            <select wire:model="previewData.{{ $index }}.category_id" class="text-[10px] border-edge rounded p-1 w-full">
                                                <option value="">Uncategorized</option>
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-4 py-3 text-right font-mono font-medium {{ $row['type'] === 'income' ? 'text-finance-green' : 'text-finance-red' }}">
                                            {{ $row['type'] === 'income' ? '+' : '−' }}₹{{ number_format($row['amount'], 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-ink-3">No transactions could be extracted. Please check the PDF format.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(count($previewData) > 0)
                        <div class="p-4 border-t border-edge flex justify-between items-center bg-surface-2">
                            <button wire:click="$set('step', 1)" class="text-xs font-medium text-ink-2">Cancel</button>
                            <button wire:click="import" class="bg-finance-green text-white px-8 py-2 rounded font-bold hover:brightness-110">
                                Import {{ count($previewData) }} Transactions
                            </button>
                        </div>
                    @endif
                </x-panel>
            </div>
        @endif
    </div>
</div>
