<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'period',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // ── Relationships ──────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    // ── Helpers ────────────────────────────────────────────

    public function spentAmount(): float
    {
        // Calculated in TransactionService — placeholder for relationship access
        return Transaction::where('user_id', $this->user_id)
            ->when($this->category_id, fn($q) => $q->where('category_id', $this->category_id))
            ->expense()
            ->whereBetween('transaction_date', [$this->start_date, $this->end_date ?? now()])
            ->sum('amount');
    }

    public function remainingAmount(): float
    {
        return max(0, (float)$this->amount - $this->spentAmount());
    }

    public function percentUsed(): float
    {
        if ((float)$this->amount === 0.0) return 0;
        return round(($this->spentAmount() / (float)$this->amount) * 100, 1);
    }
}
