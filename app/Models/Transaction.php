<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        // Bank API fields
        'transaction_id',
        'date',
        'amount',
        'currency',
        'counter_account',
        'counter_bank_code',
        'counter_bank_name',
        'counter_bic',
        'counter_account_name',
        'ks',
        'vs',
        'ss',
        'user_identification',
        'recipient_message',
        'transaction_type',
        'executor',
        'comment',
        'instruction_id',
        'payer_reference',

        // Our additional fields
        'operation_type',
        'expense_type',
        'income_type',
        'description',
        'document_number',
        'confirmation_number',

        // Future relationships
        'donor_id',
        'supplier_id',
        'campaign_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Get the bank account that owns the transaction.
     */
    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    /**
     * Get the organization through bank account.
     */
    public function organization(): BelongsTo
    {
        return $this->bankAccount->organization();
    }

    /**
     * Scope a query to only include income transactions.
     */
    public function scopeIncome($query)
    {
        return $query->where('operation_type', 'income');
    }

    /**
     * Scope a query to only include expense transactions.
     */
    public function scopeExpense($query)
    {
        return $query->where('operation_type', 'expense');
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to search by variable symbol (VS).
     */
    public function scopeByVs($query, $vs)
    {
        return $query->where('vs', $vs);
    }
}
