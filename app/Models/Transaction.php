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
     * @var array<string>
     */
    protected $fillable = [
        'bank_account_id',

        // FioBanka API
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
        'specification',

        // Курс валюти
        'exchange_rate',
        'amount_czk',

        // Додаткові поля
        'operation_type',
        'expense_type',
        'income_type',
        'description',
        'document_number',
        'confirmation_number',

        // Зв'язки
        'donor_id',
        'supplier_id',
        'campaign_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'amount' => 'decimal:2',
            'exchange_rate' => 'decimal:4',
            'amount_czk' => 'decimal:2',
        ];
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function organization(): BelongsTo
    {
        return $this->bankAccount->organization();
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function isIncome(): bool
    {
        return $this->operation_type === 'income' || $this->amount >= 0;
    }

    public function isExpense(): bool
    {
        return $this->operation_type === 'expense' || $this->amount < 0;
    }

    public function isForeignCurrency(): bool
    {
        return $this->currency !== 'CZK';
    }

    public function scopeIncome($query)
    {
        return $query->where('operation_type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('operation_type', 'expense');
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeByVs($query, $vs)
    {
        return $query->where('vs', $vs);
    }

    public function scopeForBankAccount($query, $bankAccountId)
    {
        return $query->where('bank_account_id', $bankAccountId);
    }

    public function scopeForeignCurrency($query)
    {
        return $query->where('currency', '!=', 'CZK');
    }
}
