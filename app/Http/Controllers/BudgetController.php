<?php

namespace App\Http\Controllers;

use App\Http\Requests\BudgetRequest;
use App\Models\Budget;
use App\Services\BudgetService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    use AuthorizesRequests;

    protected $budgetService;

    public function __construct(BudgetService $budgetService)
    {
        $this->budgetService = $budgetService;
    }

    public function index(Request $request)
    {
        $month = $request->integer('month', (int) now()->month);
        $year = $request->integer('year', (int) now()->year);

        $budgets = $this->budgetService->getBudgetBreakdown($request->user()->id, $month, $year);

        return view('budgets.index', compact('budgets'));
    }

    public function store(BudgetRequest $request)
    {
        $budget = $request->user()->budgets()->create($request->validated());

        return response()->json($budget, 201);
    }

    public function show(Budget $budget)
    {
        $this->authorize('view', $budget);

        return response()->json($budget->load('category'));
    }

    public function update(BudgetRequest $request, Budget $budget)
    {
        $this->authorize('update', $budget);
        $budget->update($request->validated());

        return response()->json($budget);
    }

    public function destroy(Budget $budget)
    {
        $this->authorize('delete', $budget);
        $budget->delete();

        return response()->json(null, 204);
    }
}
