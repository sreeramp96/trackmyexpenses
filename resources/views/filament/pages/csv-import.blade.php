<x-filament-panels::page>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap');

        .import-container {
            font-family: 'Bricolage Grotesque', sans-serif;
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Sleek Technical Stepper */
        .stepper-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3rem;
            position: relative;
            padding: 0 1rem;
        }

        .stepper-header::before {
            content: '';
            position: absolute;
            top: 1.25rem;
            left: 0;
            right: 0;
            height: 1px;
            background: #e5e7eb;
            z-index: 0;
        }

        .step-pill {
            position: relative;
            z-index: 1;
            background: #f5f4f0;
            padding: 0 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .step-dot {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            font-weight: 800;
            border: 1px solid #d1d5db;
            background: #fff;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .step-pill.active .step-dot {
            border-color: #1a1916;
            background: #1a1916;
            color: #fff;
            box-shadow: 0 0 0 4px rgba(26, 25, 22, 0.1);
            transform: scale(1.1);
        }

        .step-pill.done .step-dot {
            border-color: #166534;
            background: #166534;
            color: #fff;
        }

        .step-label {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #9ca3af;
            transition: color 0.3s;
        }

        .step-pill.active .step-label {
            color: #1a1916;
        }

        /* Modern Staging Area */
        .main-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 1.25rem;
            padding: 2.5rem;
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05);
        }

        .card-header {
            margin-bottom: 2.5rem;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .card-title {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #1a1916;
        }

        .card-subtitle {
            font-size: 0.9rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        /* Interactive Staging Table */
        .staging-table-container {
            margin-top: 2rem;
            border: 1px solid #e5e7eb;
            border-radius: 1rem;
            overflow-x: auto;
            background: #fff;
        }

        .staging-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        .staging-table th {
            background: #f9fafb;
            padding: 1rem;
            text-align: left;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }

        .staging-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f3f4f6;
            transition: background 0.2s;
        }

        .staging-table tr:last-child td { border-bottom: none; }
        .staging-table tr:hover td { background: rgba(249, 250, 251, 0.8); }

        .input-minimal {
            width: 100%;
            background: transparent;
            border: 1px solid transparent;
            border-radius: 0.5rem;
            padding: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
            outline: none;
        }

        .input-minimal:focus {
            background: #fff;
            border-color: #1a1916;
            box-shadow: 0 0 0 3px rgba(26, 25, 22, 0.05);
        }

        .input-minimal.mono {
            font-family: ui-monospace, monospace;
            font-size: 0.8rem;
        }

        /* Action Buttons */
        .action-footer {
            margin-top: 3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 2rem;
            border-top: 1px solid #f3f4f6;
        }

        /* Type Toggles */
        .type-pill-group {
            display: inline-flex;
            background: #f3f4f6;
            padding: 0.25rem;
            border-radius: 0.75rem;
        }

        .type-pill {
            padding: 0.35rem 1rem;
            font-size: 0.65rem;
            font-weight: 800;
            border-radius: 0.6rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .type-pill.active-in { background: #166534; color: #fff; }
        .type-pill.active-out { background: #991b1b; color: #fff; }
        .type-pill.inactive { color: #6b7280; }

        .badge-status {
            background: #1a1916;
            color: #fff;
            padding: 0.4rem 1rem;
            border-radius: 99px;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.05em;
        }
    </style>

    <div class="import-container">
        {{-- Stepper --}}
        <div class="stepper-header">
            <div class="step-pill {{ $step >= 1 ? ($step > 1 ? 'done' : 'active') : '' }}">
                <div class="step-dot">{{ $step > 1 ? '✓' : '1' }}</div>
                <div class="step-label">Upload</div>
            </div>
            <div class="step-pill {{ $step >= 2 ? ($step > 2 ? 'done' : 'active') : '' }}">
                <div class="step-dot">{{ $step > 2 ? '✓' : '2' }}</div>
                <div class="step-label">Mapping</div>
            </div>
            <div class="step-pill {{ $step === 3 ? 'active' : '' }}">
                <div class="step-dot">3</div>
                <div class="step-label">Review</div>
            </div>
        </div>

        <form wire:submit="import">
            @if($step <= 2)
                <div class="main-card animate-in fade-in zoom-in-95 duration-500">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">{{ $step === 1 ? 'Source Data' : 'Column Mapping' }}</h2>
                            <p class="card-subtitle">{{ $step === 1 ? 'Upload your bank statement to begin extraction.' : 'Tell us which columns contain your transaction details.' }}</p>
                        </div>
                        <div class="badge-status">SECURE PIPELINE</div>
                    </div>

                    <div class="space-y-8 pb-32">
                        {{ $this->form }}
                    </div>

                    @if($step === 2)
                        <div class="flex justify-end gap-4 pt-4 border-t border-gray-100">
                            <x-filament::button wire:click="$set('step', 1)" color="gray" variant="ghost">
                                Change File
                            </x-filament::button>
                            <x-filament::button wire:click="generatePreview" size="lg" icon="heroicon-m-sparkles" class="px-8 shadow-lg">
                                Analyze Statement
                            </x-filament::button>
                        </div>
                    @endif
                </div>
            @endif

            @if($step === 3)
                <div class="animate-in fade-in slide-in-from-bottom-8 duration-700">
                    <div class="flex items-end justify-between mb-8">
                        <div>
                            <h2 class="text-3xl font-black tracking-tight uppercase">Staging Area</h2>
                            <p class="text-sm text-[#6b7280]">Adjust extracted data before final database commitment.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Transactions Ready</span>
                            <div class="bg-[#1a1916] text-white px-5 py-2 rounded-xl text-sm font-black shadow-xl">
                                {{ count($previewData) }}
                            </div>
                        </div>
                    </div>

                    <div class="staging-table-container shadow-2xl">
                        <table class="staging-table">
                            <thead>
                                <tr>
                                    <th class="w-40">Transaction Date</th>
                                    <th>Description / Narration</th>
                                    <th class="w-48">Category</th>
                                    <th class="w-32 text-center">Direction</th>
                                    <th class="text-right w-36">Amount</th>
                                    <th class="w-16 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($previewData as $index => $tx)
                                    <tr wire:key="tx-{{ $index }}">
                                        <td>
                                            <input type="date" wire:model="previewData.{{ $index }}.transaction_date" class="input-minimal mono">
                                        </td>
                                        <td>
                                            <input type="text" wire:model="previewData.{{ $index }}.note" class="input-minimal">
                                        </td>
                                        <td>
                                            <select wire:model="previewData.{{ $index }}.category_id" class="input-minimal text-xs appearance-none cursor-pointer">
                                                <option value="">Uncategorized</option>
                                                @foreach($categories as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <div class="type-pill-group">
                                                <div wire:click="$set('previewData.{{ $index }}.type', 'income')" class="type-pill {{ $tx['type'] === 'income' ? 'active-in' : 'inactive' }}">IN</div>
                                                <div wire:click="$set('previewData.{{ $index }}.type', 'expense')" class="type-pill {{ $tx['type'] === 'expense' ? 'active-out' : 'inactive' }}">OUT</div>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            <div class="flex items-center justify-end font-mono font-black {{ $tx['type'] === 'income' ? 'text-[#166534]' : 'text-[#991b1b]' }}">
                                                <span class="mr-1">{{ $tx['type'] === 'expense' ? '−' : '+' }}</span>
                                                <input type="number" step="0.01" wire:model="previewData.{{ $index }}.amount" class="w-24 bg-transparent border-none p-1 text-right focus:outline-none">
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" wire:click="removeItem({{ $index }})" class="text-gray-400 hover:text-[#991b1b] transition-all transform hover:rotate-90 hover:scale-125" title="Remove row">
                                                <x-heroicon-m-trash class="w-5 h-5 mx-auto" />
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="action-footer">
                        <x-filament::button wire:click="$set('step', 2)" color="gray" variant="ghost" icon="heroicon-m-arrow-uturn-left">
                            Back to Mapping
                        </x-filament::button>

                        <x-filament::button type="submit" size="xl" color="success" icon="heroicon-m-cloud-arrow-up" class="px-16 shadow-2xl font-black italic">
                            FINALIZE & SYNC DATA
                        </x-filament::button>
                    </div>
                </div>
            @endif
        </form>
    </div>
</x-filament-panels::page>
