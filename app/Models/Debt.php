<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Debt extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = [
        'user_id',
        'contact_name',
        'direction',
        'amount',
        'remaining_amount',
        'due_date',
        'note',
        'is_settled',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'due_date' => 'date',
        'is_settled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeLent($query)
    {
        return $query->where('direction', 'lent');
    }

    public function scopeBorrowed($query)
    {
        return $query->where('direction', 'borrowed');
    }

    public function scopeUnsettled($query)
    {
        return $query->where('is_settled', false);
    }

    public function scopeOverdue($query)
    {
        return $query->unsettled()
            ->where('due_date', '<', now()->startOfDay());
    }

    // ── Helpers ────────────────────────────────────────────

    public function isOverdue(): bool
    {
        return ! $this->is_settled
            && $this->due_date
            && $this->due_date->isPast();
    }

    public function label(): string
    {
        return $this->direction === 'lent' ? 'To collect' : 'To pay';
    }
}
