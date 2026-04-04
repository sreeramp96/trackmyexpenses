<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 pt-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach(['income', 'expense', 'transfer'] as $t)
                <x-panel wire:key="panel-{{ $t }}" :title="ucfirst($t) . ' Categories'" :color="$t === 'income' ? 'green' : ($t === 'expense' ? 'red' : 'blue')">
                    <x-slot name="action">
                        <button type="button" wire:click="openModal(null, '{{ $t }}')" class="text-[10px] font-mono font-medium text-ink-3 hover:text-ink uppercase tracking-wider transition-colors">
                            + Add
                        </button>
                    </x-slot>
                    <div class="divide-y divide-edge">
                        @php
                            $typeCategories = $categories->where('type', $t);
                        @endphp
                        @forelse($typeCategories as $category)
                            <div wire:key="cat-row-{{ $category->id }}" class="group relative flex items-center justify-between p-4 hover:bg-surface-2 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $category->color }}"></div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-ink truncate">{{ $category->name }}</p>
                                        @if($category->parent_id)
                                            <p class="text-[10px] text-ink-3 uppercase tracking-tighter">Sub of {{ $category->parent->name }}</p>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-3 shrink-0">
                                    <span class="text-[10px] font-mono text-ink-3">{{ $category->transactions_count }} txs</span>
                                    
                                    @if($category->user_id)
                                        <div class="flex items-center gap-1.5 opacity-40 group-hover:opacity-100 transition-opacity">
                                            <button type="button" wire:click="openModal({{ $category->id }})" class="text-ink-3 hover:text-ink transition-colors p-1" title="Edit">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </button>
                                            <button type="button" wire:click="delete({{ $category->id }})" wire:confirm="Are you sure you want to delete this category?" class="text-ink-3 hover:text-finance-red transition-colors p-1" title="Delete">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-[10px] bg-surface-3 px-1.5 py-0.5 rounded text-ink-3 uppercase tracking-widest font-mono">System</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div wire:key="cat-none-{{ $t }}" class="p-4 text-center text-[10px] text-ink-3 italic uppercase tracking-widest">No categories</div>
                        @endforelse
                    </div>
                </x-panel>
            @endforeach
        </div>
    </div>

    {{-- Custom Modal Implementation --}}
    @if($showModal)
        <div wire:key="cat-modal-bg" class="fixed inset-0 z-50 flex items-center justify-center bg-ink/40" wire:click.self="$set('showModal', false)">
            <div class="bg-surface border border-edge-2 rounded w-full max-w-md shadow-xl" @click.stop>
                <div class="flex items-center justify-between px-4 py-3 border-b border-edge">
                    <h2 class="text-sm font-medium">{{ $categoryId ? 'Edit' : 'New' }} category</h2>
                    <button type="button" wire:click="$set('showModal', false)" class="text-ink-3 hover:text-ink text-lg leading-none px-1">
                        ×
                    </button>
                </div>

                <div class="p-4 space-y-4">
                    {{-- Type selector --}}
                    <div>
                        <label class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1.5">Type</label>
                        <div class="flex border border-edge rounded overflow-hidden">
                            @foreach(['expense'=>'Expense','income'=>'Income','transfer'=>'Transfer'] as $val=>$label)
                                <button type="button" wire:key="type-btn-{{ $val }}" wire:click="$set('type','{{ $val }}')"
                                        class="flex-1 py-1.5 text-xs font-medium transition-all border-r border-edge last:border-r-0
                  {{ $type===$val
                    ? ($val==='income' ? 'bg-finance-green-bg text-finance-green' : ($val==='expense' ? 'bg-finance-red-bg text-finance-red' : 'bg-finance-blue-bg text-finance-blue'))
                    : 'bg-surface text-ink-2 hover:bg-surface-2' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Name --}}
                    <div>
                        <label class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1">Name</label>
                        <input wire:model="name" type="text" placeholder="Category name"
                               class="w-full border border-edge-2 rounded px-3 py-2 text-sm font-medium bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none">
                        @error('name')<p class="text-[10px] text-finance-red mt-1 uppercase font-mono tracking-tighter">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Color --}}
                        <div>
                            <label class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1">Color</label>
                            <div class="flex items-center gap-2">
                                <input wire:model="color" type="color" class="h-8 w-10 border-none p-0 bg-transparent cursor-pointer rounded overflow-hidden">
                                <input wire:model="color" type="text" placeholder="#94a3b8"
                                       class="w-full border border-edge-2 rounded px-2.5 py-1.5 text-[10px] font-mono bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none">
                            </div>
                            @error('color')<p class="text-[10px] text-finance-red mt-1 uppercase font-mono tracking-tighter">{{ $message }}</p>@enderror
                        </div>

                        {{-- Parent --}}
                        <div>
                            <label class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1">Parent (Optional)</label>
                            <select wire:model="parentId"
                                    class="w-full border border-edge-2 rounded px-2.5 py-1.5 text-xs bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none pr-8">
                                <option value="">None</option>
                                @foreach($parentOptions->where('type', $type) as $option)
                                    <option wire:key="parent-opt-{{ $option->id }}" value="{{ $option->id }}">{{ $option->name }}</option>
                                @endforeach
                            </select>
                            @error('parentId')<p class="text-[10px] text-finance-red mt-1 uppercase font-mono tracking-tighter">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 px-4 py-3 border-t border-edge bg-surface-2">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="text-xs px-3 py-1.5 border border-edge rounded bg-surface text-ink-2 hover:bg-surface-3 transition-colors">
                        Cancel
                    </button>
                    <button type="button" wire:click="save"
                            class="text-xs px-4 py-1.5 bg-ink text-white rounded hover:bg-ink-2 transition-colors font-medium">
                        {{ $categoryId ? 'Update' : 'Create Category' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
