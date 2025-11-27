<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'account_name',
        'bank_name',
        'organization_id',
        'bank_code',
        'account_number',
        'iban',
        'currency',
        'is_transparent',
        'api_token',
        'is_active',
        'last_sync_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_transparent' => 'boolean',
            'is_active' => 'boolean',
            'last_sync_at' => 'datetime'
        ];
    }

    /**
     * Get the organization that owns the bank account.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get all transactions for the bank account.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Scope a query to only include active accounts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
