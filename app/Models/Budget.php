<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

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

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // ── Calculations ───────────────────────────────────────

    public function spentAmount(): float
    {
        $query = Transaction::where('user_id', $this->user_id)
            ->where('transaction_date', '>=', $this->start_date);

        if ($this->end_date) {
            $query->where('transaction_date', '<=', $this->end_date);
        } else {
            $query->where('transaction_date', '<=', $this->start_date->copy()->endOfMonth());
        }

        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        } else {
            $query->where('type', 'expense');
        }

        return (float) $query->sum('amount');
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
