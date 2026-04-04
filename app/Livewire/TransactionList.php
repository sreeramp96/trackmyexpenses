<?php

namespace App\Livewire;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionList extends Component
{
    use WithPagination;

    public $search = '';
    public $type = '';
    public $accountId = '';
    public $categoryId = '';
    public $startDate;
    public $endDate;
    public $sortBy = 'transaction_date';
    public $sortDir = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
        'accountId' => ['except' => ''],
        'categoryId' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'sortBy' => ['except' => 'transaction_date'],
        'sortDir' => ['except' => 'desc'],
    ];

    protected $listeners = ['transactionSaved' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    public function updatingAccountId()
    {
        $this->resetPage();
    }

    public function updatingCategoryId()
    {
        $this->resetPage();
    }

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
    }

    public function resetFilters()
    {
        $this->reset(['search', 'type', 'accountId', 'categoryId', 'startDate', 'endDate']);
    }

    public function deleteTransaction($id)
    {
        $transaction = Auth::user()->transactions()->findOrFail($id);
        app(\App\Services\TransactionService::class)->delete($transaction);
        session()->flash('success', 'Transaction deleted.');
    }

    public function render()
    {
        $query = Auth::user()->transactions()
            ->with(['account', 'toAccount', 'category'])
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('note', 'like', '%' . $this->search . '%')
                        ->orWhere('reference_number', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->when($this->accountId, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('account_id', $this->accountId)
                        ->orWhere('to_account_id', $this->accountId);
                });
            })
            ->when($this->categoryId, fn ($q) => $q->where('category_id', $this->categoryId))
            ->when($this->startDate, fn ($q) => $q->where('transaction_date', '>=', $this->startDate))
            ->when($this->endDate, fn ($q) => $q->where('transaction_date', '<=', $this->endDate))
            ->orderBy($this->sortBy, $this->sortDir);

        return view('livewire.transaction-list', [
            'transactions' => $query->paginate(20),
            'accounts' => Auth::user()->accounts()->get(),
            'categories' => Category::forUser(Auth::id())->get(),
        ])->layout('layouts.app', ['heading' => 'Transactions']);
    }
}
