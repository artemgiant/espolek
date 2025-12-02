<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonorBankAccount extends Model
{
    use HasFactory;

    /**
     * @var array<string>
     */
    protected $fillable = [
        'donor_id',
        'account_number',
        'bank_code',
        'bank_name',
        'iban',
    ];

    /**
     * Донор, якому належить рахунок.
     */
    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    /**
     * Повний номер рахунку (account_number/bank_code).
     */
    public function getFullAccountNumberAttribute(): string
    {
        if ($this->bank_code) {
            return "{$this->account_number}/{$this->bank_code}";
        }

        return $this->account_number;
    }
}
