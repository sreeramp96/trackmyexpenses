<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Account extends Model
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
        'name',
        'type',
        'balance',
        'currency',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        // Transactions where this account is the SOURCE
        return $this->hasMany(Transaction::class, 'account_id');
    }

    public function toTransactions(): HasMany
    {
        // Transactions where this account is the DESTINATION (transfers)
        return $this->hasMany(Transaction::class, 'to_account_id');
    }

    // ── Helpers ────────────────────────────────────────────

    public function isCredit(): bool
    {
        return $this->type === 'credit_card';
    }

    public function formattedBalance(): string
    {
        return $this->currency.' '.number_format($this->balance, 2);
    }
}
