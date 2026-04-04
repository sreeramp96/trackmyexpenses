<?php

namespace App\Livewire;

use App\Models\Debt;
use App\Services\DebtService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DebtManager extends Component
{
    public $contact_name;
    public $direction = 'lent';
    public $amount;
    public $remaining_amount;
    public $due_date;
    public $note;
    
    public $filterDirection = '';
    public $filterStatus = 'active';

    public $editingId = null;
    public $showModal = false;

    protected $listeners = ['transactionSaved' => '$refresh'];

    protected function rules()
    {
        return [
            'contact_name' => 'required|string|max:255',
            'direction' => 'required|in:lent,borrowed',
            'amount' => 'required|numeric|min:0.01',
            'remaining_amount' => 'required|numeric|min:0|max:' . $this->amount,
            'due_date' => 'nullable|date',
            'note' => 'nullable|string',
        ];
    }

    public function render(DebtService $debtService)
    {
        $query = Auth::user()->debts();

        if ($this->filterDirection) {
            $query->where('direction', $this->filterDirection);
        }

        if ($this->filterStatus === 'active') {
            $query->unsettled();
        } elseif ($this->filterStatus === 'settled') {
            $query->where('is_settled', true);
        } elseif ($this->filterStatus === 'overdue') {
            $query->overdue();
        }

        $debts = $query->latest()->get();
        $summary = $debtService->getDebtSummary(Auth::id());

        return view('livewire.debt-manager', [
            'debts' => $debts,
            'summary' => $summary,
        ])->layout('layouts.app', ['heading' => 'Debts']);
    }

    public function openModal($id = null)
    {
        $this->reset(['contact_name', 'direction', 'amount', 'remaining_amount', 'due_date', 'note', 'editingId']);
        
        if ($id) {
            $debt = Debt::findOrFail($id);
            $this->authorize('update', $debt);
            
            $this->editingId = $id;
            $this->contact_name = $debt->contact_name;
            $this->direction = $debt->direction;
            $this->amount = $debt->amount;
            $this->remaining_amount = $debt->remaining_amount;
            $this->due_date = $debt->due_date?->format('Y-m-d');
            $this->note = $debt->note;
        } else {
            $this->direction = 'lent';
            $this->amount = '';
            $this->remaining_amount = '';
        }

        $this->showModal = true;
    }

    public function updatedAmount($value)
    {
        if (!$this->editingId) {
            $this->remaining_amount = $value;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'contact_name' => $this->contact_name,
            'direction' => $this->direction,
            'amount' => $this->amount,
            'remaining_amount' => $this->remaining_amount,
            'due_date' => $this->due_date ?: null,
            'note' => $this->note ?: null,
            'is_settled' => $this->remaining_amount <= 0,
        ];

        if ($this->editingId) {
            $debt = Debt::findOrFail($this->editingId);
            $this->authorize('update', $debt);
            $debt->update($data);
            session()->flash('success', 'Debt updated successfully.');
        } else {
            Auth::user()->debts()->create($data);
            session()->flash('success', 'Debt recorded successfully.');
        }

        $this->showModal = false;
    }

    public function delete($id)
    {
        $debt = Debt::findOrFail($id);
        $this->authorize('delete', $debt);
        $debt->delete();

        session()->flash('success', 'Debt deleted successfully.');
    }

    public function settle($id)
    {
        $debt = Debt::findOrFail($id);
        $this->authorize('update', $debt);
        
        $debt->update([
            'remaining_amount' => 0,
            'is_settled' => true,
        ]);

        session()->flash('success', 'Debt marked as settled.');
    }

    public function recordPayment($id)
    {
        $this->dispatch('open-tx-modal', debtId: $id);
    }
}
