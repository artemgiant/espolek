<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\TransactionCategory;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        // Зв'язок
        'bank_account_id',

        // Поля з FioBanka API
        'transaction_id',           // column22 - ID pohybu
        'date',                     // column0  - Datum
        'amount',                   // column1  - Objem
        'currency',                 // column14 - Měna
        'counter_account',          // column2  - Protiúčet
        'counter_bank_code',        // column3  - Kód banky
        'counter_bank_name',        // column12 - Název banky
        'counter_bic',              // не приходить з API, завжди null
        'counter_account_name',     // column10 - Název protiúčtu
        'ks',                       // column4  - Konstantní symbol
        'vs',                       // column5  - Variabilní symbol
        'ss',                       // column6  - Specifický symbol
        'user_identification',      // column7  - Uživatelská identifikace
        'recipient_message',        // column16 - Zpráva pro příjemce
        'transaction_type',         // column8  - Typ
        'executor',                 // column9  - Provedl
        'comment',                  // column25 - Komentář
        'instruction_id',           // column17 - ID pokynu
        'specification',            // column18 - Upřesnění (напр. "1225.00 EUR")

        // Наші додаткові поля
        'operation_type',           // income / expense
        'expense_type',             // taxable / non_taxable
        'category',  // замість income_type
        'description',
        'document_number',
        'confirmation_number',

        // Майбутні зв'язки
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
            'category' => TransactionCategory::class,
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
     * Чи є транзакція доходом.
     */
    public function isIncome(): bool
    {
        return $this->operation_type === 'income' || $this->amount >= 0;
    }

    /**
     * Чи є транзакція витратою.
     */
    public function isExpense(): bool
    {
        return $this->operation_type === 'expense' || $this->amount < 0;
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

    /**
     * Scope a query to filter by bank account.
     */
    public function scopeForBankAccount($query, $bankAccountId)
    {
        return $query->where('bank_account_id', $bankAccountId);
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }
}
