<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Budget extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

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

    public function scopeActiveForPeriod($query, int $month, int $year)
    {
        $periodStart = now()->setYear($year)->setMonth($month)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        //        return $query->where('start_date', '<=', now())
        //            ->where(function ($q) {
        //                $q->whereNull('end_date')
        //                    ->orWhere('end_date', '>=', now());
        //            });
        return $query
            ->where('start_date', '<=', $periodEnd)
            ->where(function ($q) use ($periodStart) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', $periodStart);
            });
    }

    // ── Helpers ────────────────────────────────────────────

    public function spentAmount(): float
    {
        // Calculated in TransactionService — placeholder for relationship access
        return Transaction::where('user_id', $this->user_id)
            ->when($this->category_id, fn ($q) => $q->where('category_id', $this->category_id))
            ->expense()
            ->whereBetween('transaction_date', [$this->start_date, $this->end_date ?? now()])
            ->sum('amount');
    }

    public function remainingAmount(): float
    {
        return max(0, (float) $this->amount - $this->spentAmount());
    }

    public function percentUsed(): float
    {
        if ((float) $this->amount === 0.0) {
            return 0;
        }

        return round(($this->spentAmount() / (float) $this->amount) * 100, 1);
    }
}
