<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    use AuthorizesRequests;

    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        $transactions = $request->user()->transactions()
            ->with(['account', 'toAccount', 'category'])
            ->latest('transaction_date')
            ->paginate(20);

        return view('transactions.index', compact('transactions'));
    }

    public function store(TransactionRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $transaction = $request->user()->transactions()->create();

        return response()->json($transaction, 201);
    }

    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);

        return response()->json($transaction->load(['account', 'toAccount', 'category']));
    }

    public function update(TransactionRequest $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        $updatedTransaction = $this->transactionService->update($transaction, $request->validated());

        return response()->json($updatedTransaction);
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);

        $this->transactionService->delete($transaction);

        return response()->json(null, 204);
    }
}
