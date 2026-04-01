<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Http\Requests\AccountRequest;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AccountController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $accounts = $request->user()->accounts()->get();
        return response()->json($accounts);
    }

    public function store(AccountRequest $request)
    {
        $account = $request->user()->accounts()->create($request->validated());
        return response()->json($account, 201);
    }

    public function show(Account $account)
    {
        $this->authorize('view', $account);
        return response()->json($account);
    }

    public function update(AccountRequest $request, Account $account)
    {
        $this->authorize('update', $account);
        $account->update($request->validated());
        return response()->json($account);
    }

    public function destroy(Account $account)
    {
        $this->authorize('delete', $account);
        $account->delete();
        return response()->json(null, 204);
    }
}
