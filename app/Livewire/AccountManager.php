<?php

namespace App\Livewire;

use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AccountManager extends Component
{
    public $name;
    public $type = 'bank';
    public $balance = 0;
    public $currency = 'INR';
    public $editingId = null;
    public $showModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'type' => 'required|in:bank,cash,credit_card,wallet,loan',
        'balance' => 'required|numeric',
        'currency' => 'required|string|max:10',
    ];

    public function render()
    {
        $accounts = Auth::user()->accounts()->get();

        return view('livewire.account-manager', [
            'accounts' => $accounts,
        ])->layout('layouts.app', ['heading' => 'Accounts']);
    }

    public function openModal($id = null)
    {
        $this->reset(['name', 'type', 'balance', 'currency', 'editingId']);

        if ($id) {
            $account = Account::findOrFail($id);
            $this->authorize('update', $account);

            $this->editingId = $id;
            $this->name = $account->name;
            $this->type = $account->type;
            $this->balance = $account->balance;
            $this->currency = $account->currency;
        } else {
            $this->balance = 0;
            $this->currency = 'INR';
            $this->type = 'bank';
        }

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editingId) {
            $account = Account::findOrFail($this->editingId);
            $this->authorize('update', $account);
            $account->update([
                'name' => $this->name,
                'type' => $this->type,
                'balance' => $this->balance,
                'currency' => $this->currency,
            ]);
            session()->flash('success', 'Account updated successfully.');
        } else {
            Auth::user()->accounts()->create([
                'name' => $this->name,
                'type' => $this->type,
                'balance' => $this->balance,
                'currency' => $this->currency,
            ]);
            session()->flash('success', 'Account created successfully.');
        }

        $this->showModal = false;
    }

    public function delete($id)
    {
        $account = Account::findOrFail($id);
        $this->authorize('delete', $account);
        $account->delete();

        session()->flash('success', 'Account deleted successfully.');
    }
}
