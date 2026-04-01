<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Debt extends Model
{
    use SoftDeletes;

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

    // ── Relationships ──────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeUnsettled($query)
    {
        return $query->where('is_settled', false);
    }

    public function scopeLent($query)
    {
        return $query->where('direction', 'lent');
    }

    public function scopeBorrowed($query)
    {
        return $query->where('direction', 'borrowed');
    }

    public function scopeOverdue($query)
    {
        return $query->unsettled()
            ->whereNotNull('due_date')
            ->where('due_date', '<', now());
    }

    // ── Helpers ────────────────────────────────────────────

    public function isOverdue(): bool
    {
        return !$this->is_settled
            && $this->due_date
            && $this->due_date->isPast();
    }

    public function label(): string
    {
        return $this->direction === 'lent' ? 'To collect' : 'To pay';
    }
}
