<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Http\Requests\BudgetRequest;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BudgetController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $budgets = $request->user()->budgets()->with('category')->get();
        return response()->json($budgets);
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
