<div class="p-6">
    <x-slot name="heading">Import CSV</x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <nav class="flex items-center justify-center space-x-4">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold {{ $step >= 1 ? 'bg-ink text-white' : 'bg-surface-3 text-ink-3' }}">1</span>
                    <span class="text-sm font-medium {{ $step >= 1 ? 'text-ink' : 'text-ink-3' }}">Upload</span>
                </div>
                <div class="w-12 h-px bg-edge"></div>
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold {{ $step >= 2 ? 'bg-ink text-white' : 'bg-surface-3 text-ink-3' }}">2</span>
                    <span class="text-sm font-medium {{ $step >= 2 ? 'text-ink' : 'text-ink-3' }}">Map Columns</span>
                </div>
                <div class="w-12 h-px bg-edge"></div>
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold {{ $step >= 3 ? 'bg-ink text-white' : 'bg-surface-3 text-ink-3' }}">3</span>
                    <span class="text-sm font-medium {{ $step >= 3 ? 'text-ink' : 'text-ink-3' }}">Preview</span>
                </div>
            </nav>
        </div>

        @if($step === 1)
            <x-panel title="Upload CSV File">
                <div class="p-8 text-center">
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-ink-3 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-sm text-ink-2 mb-4">Select your HDFC or Google Sheets CSV file to begin.</p>
                        <input type="file" wire:model="file" class="hidden" id="csv-upload" accept=".csv">
                        <label for="csv-upload" class="cursor-pointer bg-ink text-white px-6 py-2 rounded font-medium hover:bg-ink-2 transition-colors">
                            Choose File
                        </label>
                        <div wire:loading wire:target="file" class="mt-4 text-xs text-ink-3">Parsing file...</div>
                    </div>
                </div>
            </x-panel>
        @endif

        @if($step === 2)
            <x-panel title="Map Your Columns" color="blue">
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-mono font-bold uppercase text-ink-3 mb-1">Target Account</label>
                                <select wire:model="accountId" class="w-full text-xs border-edge rounded pr-8 py-2">
                                    <option value="">Select Account</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->name }} (₹{{ number_format($acc->balance, 0) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-mono font-bold uppercase text-ink-3 mb-1">Date Column</label>
                                <select wire:model="mapDate" class="w-full text-xs border-edge rounded pr-8 py-2">
                                    <option value="">Select Column</option>
                                    @foreach($headers as $h) <option value="{{ $h }}">{{ $h }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-mono font-bold uppercase text-ink-3 mb-1">Description Column</label>
                                <select wire:model="mapDescription" class="w-full text-xs border-edge rounded pr-8 py-2">
                                    <option value="">Select Column</option>
                                    @foreach($headers as $h) <option value="{{ $h }}">{{ $h }}</option> @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-mono font-bold uppercase text-ink-3 mb-1">Debits (Expenses)</label>
                                <select wire:model="mapDebit" class="w-full text-xs border-edge rounded pr-8 py-2">
                                    <option value="">Select Column</option>
                                    @foreach($headers as $h) <option value="{{ $h }}">{{ $h }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-mono font-bold uppercase text-ink-3 mb-1">Credits (Income)</label>
                                <select wire:model="mapCredit" class="w-full text-xs border-edge rounded pr-8 py-2">
                                    <option value="">Select Column</option>
                                    @foreach($headers as $h) <option value="{{ $h }}">{{ $h }}</option> @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button wire:click="generatePreview" class="bg-ink text-white px-6 py-2 rounded font-medium">
                            Generate Preview
                        </button>
                    </div>
                </div>
            </x-panel>
        @endif

        @if($step === 3)
            <x-panel title="Preview & Auto-Categorization" color="green">
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
                            @foreach($previewData as $index => $row)
                                <tr class="border-b border-edge last:border-0">
                                    <td class="px-4 py-3 font-mono">{{ $row['transaction_date'] }}</td>
                                    <td class="px-4 py-3 truncate max-w-[200px]">{{ $row['note'] }}</td>
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-edge flex justify-between items-center bg-surface-2">
                    <button wire:click="$set('step', 2)" class="text-xs font-medium text-ink-2 hover:text-ink">Back to mapping</button>
                    <button wire:click="import" class="bg-finance-green text-white px-8 py-2 rounded font-bold hover:brightness-110">
                        Import {{ count($previewData) }} Transactions
                    </button>
                </div>
            </x-panel>
        @endif
    </div>
</div>
