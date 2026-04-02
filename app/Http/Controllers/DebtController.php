<?php

namespace App\Http\Controllers;

use App\Http\Requests\DebtRequest;
use App\Models\Debt;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $debts = $request->user()->debts()->latest()->get();

        return view('debts.index', compact('debts'));
    }

    public function store(DebtRequest $request)
    {
        $debt = $request->user()->debts()->create($request->validated());

        return response()->json($debt, 201);
    }

    public function show(Debt $debt)
    {
        $this->authorize('view', $debt);

        return response()->json($debt);
    }

    public function update(DebtRequest $request, Debt $debt)
    {
        $this->authorize('update', $debt);
        $debt->update($request->validated());

        return response()->json($debt);
    }

    public function destroy(Debt $debt)
    {
        $this->authorize('delete', $debt);
        $debt->delete();

        return response()->json(null, 204);
    }
}
