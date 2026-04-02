<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'account_id',
        'to_account_id',
        'category_id',
        'debt_id',
        'type',
        'amount',
        'note',
        'transaction_date',
        'reference_number',
        'is_reconciled',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'is_reconciled' => 'boolean',
        'debt_id' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function debt(): BelongsTo
    {
        return $this->belongsTo(Debt::class);
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeForMonth($query, int $month, int $year)
    {
        return $query->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year);
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeTransfer($query)
    {
        return $query->where('type', 'transfer');
    }

    public function scopeForAccount($query, int $accountId)
    {
        return $query->where('account_id', $accountId)
            ->orWhere('to_account_id', $accountId);
    }

    // ── Helpers ────────────────────────────────────────────

    public function isTransfer(): bool
    {
        return $this->type === 'transfer';
    }

    public function signedAmount(): float
    {
        return $this->type === 'income'
            ? (float) $this->amount
            : -(float) $this->amount;
    }
}
