<?php

namespace App\Livewire;

use App\Models\Category;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TransactionFormModal extends Component
{
    public bool $open = false;

    public ?int $editingId = null;

    public string $type = 'expense';

    public string $amount = '';

    public ?int $account_id = null;

    public ?int $to_account_id = null;

    public ?int $category_id = null;

    public string $transaction_date = '';

    public string $note = '';

    public string $reference_number = '';

    protected $listeners = ['open-tx-modal' => 'openModal'];

    protected function rules(): array
    {
        return [
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'nullable|exists:accounts,id|different:account_id',
            'category_id' => 'nullable|exists:categories,id',
            'transaction_date' => 'required|date',
            'note' => 'nullable|string|max:500',
            'reference_number' => 'nullable|string|max:255',
        ];
    }

    public function openModal(?int $id = null): void
    {
        $this->resetValidation();
        if ($id) {
            $tx = Auth::user()->transactions()->findOrFail($id);
            $this->fill([
                'editingId' => $id,
                'type' => $tx->type,
                'amount' => $tx->amount,
                'account_id' => $tx->account_id,
                'to_account_id' => $tx->to_account_id,
                'category_id' => $tx->category_id,
                'transaction_date' => $tx->transaction_date->toDateString(),
                'note' => $tx->note ?? '',
                'reference_number' => $tx->reference_number ?? '',
            ]);
        } else {
            $this->reset(['editingId', 'amount', 'note', 'reference_number', 'to_account_id', 'category_id']);
            $this->type = 'expense';
            $this->transaction_date = now()->toDateString();
            $this->account_id = Auth::user()->accounts()->value('id');
        }
        $this->open = true;
    }

    public function save(TransactionService $service): void
    {
        $this->validate();
        $data = [
            'user_id' => Auth::id(),
            'type' => $this->type,
            'amount' => $this->amount,
            'account_id' => $this->account_id,
            'to_account_id' => $this->type === 'transfer' ? $this->to_account_id : null,
            'category_id' => $this->category_id,
            'transaction_date' => $this->transaction_date,
            'note' => $this->note ?: null,
            'reference_number' => $this->reference_number ?: null,
        ];

        if ($this->editingId) {
            $tx = Auth::user()->transactions()->findOrFail($this->editingId);
            $service->update($tx, $data);
        } else {
            $service->create($data);
        }

        $this->open = false;
        $this->dispatch('transactionSaved');
        session()->flash('success', $this->editingId ? 'Transaction updated.' : 'Transaction saved.');
    }

    public function render()
    {
        return view('livewire.transaction-form-modal', [
            'accounts' => Auth::user()->accounts()->where('is_active', true)->get(),
            'categories' => Category::forUser(Auth::id())->orderBy('type')->orderBy('name')->get(),
        ]);
    }
}
