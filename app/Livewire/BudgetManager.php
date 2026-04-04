<?php

namespace App\Livewire;

use App\Models\Budget;
use App\Models\Category;
use App\Services\BudgetService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BudgetManager extends Component
{
    public $categoryId;
    public $amount;
    public $period = 'monthly';
    public $startDate;
    public $endDate;
    public $month;
    public $year;

    public $editingId = null;
    public $showModal = false;

    protected function rules()
    {
        return [
            'categoryId' => 'nullable|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'period' => 'required|in:weekly,monthly,yearly',
            'startDate' => 'required|date',
            'endDate' => 'nullable|date|after_or_equal:startDate',
        ];
    }

    public function mount()
    {
        $this->month = (int) now()->month;
        $this->year = (int) now()->year;
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
    }

    public function render(BudgetService $budgetService)
    {
        $budgets = $budgetService->getBudgetBreakdown(Auth::id(), $this->month, $this->year);
        $categories = Category::where('user_id', Auth::id())
            ->orWhereNull('user_id')
            ->where('type', 'expense')
            ->get();

        return view('livewire.budget-manager', [
            'budgets' => $budgets,
            'categories' => $categories,
        ])->layout('layouts.app', ['heading' => 'Budgets']);
    }

    public function openModal($id = null)
    {
        $this->reset(['categoryId', 'amount', 'period', 'startDate', 'endDate', 'editingId']);
        
        if ($id) {
            $budget = Budget::findOrFail($id);
            $this->authorize('update', $budget);
            
            $this->editingId = $id;
            $this->categoryId = $budget->category_id;
            $this->amount = $budget->amount;
            $this->period = $budget->period;
            $this->startDate = $budget->start_date->format('Y-m-d');
            $this->endDate = $budget->end_date?->format('Y-m-d');
        } else {
            $this->startDate = now()->setYear($this->year)->setMonth($this->month)->startOfMonth()->format('Y-m-d');
            $this->period = 'monthly';
        }

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'category_id' => $this->categoryId,
            'amount' => $this->amount,
            'period' => $this->period,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ];

        if ($this->editingId) {
            $budget = Budget::findOrFail($this->editingId);
            $this->authorize('update', $budget);
            $budget->update($data);
            session()->flash('success', 'Budget updated successfully.');
        } else {
            Auth::user()->budgets()->create($data);
            session()->flash('success', 'Budget created successfully.');
        }

        $this->showModal = false;
    }

    public function delete($id)
    {
        $budget = Budget::findOrFail($id);
        $this->authorize('delete', $budget);
        $budget->delete();

        session()->flash('success', 'Budget deleted successfully.');
    }

    public function previousMonth()
    {
        $date = now()->setYear($this->year)->setMonth($this->month)->subMonth();
        $this->month = (int) $date->month;
        $this->year = (int) $date->year;
    }

    public function nextMonth()
    {
        $date = now()->setYear($this->year)->setMonth($this->month)->addMonth();
        $this->month = (int) $date->month;
        $this->year = (int) $date->year;
    }
}
