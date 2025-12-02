<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donor extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var array<string>
     */
    protected $fillable = [
        // Тип
        'type',

        // Спільні поля
        'email',
        'phone',
        'whatsapp',
        'facebook_url',
        'x_url',
        'linkedin_url',
        'instagram_url',

        // Фізична особа
        'first_name',
        'last_name',
        'birth_date',
        'address',

        // Юридична особа
        'company_name',
        'ico',
        'dic',
        'legal_address',
        'representative_name',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    /**
     * Банківські рахунки донора.
     */
    public function bankAccounts(): HasMany
    {
        return $this->hasMany(DonorBankAccount::class);
    }

    /**
     * Транзакції донора.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Чи є фізичною особою.
     */
    public function isIndividual(): bool
    {
        return $this->type === 'individual';
    }

    /**
     * Чи є юридичною особою.
     */
    public function isCompany(): bool
    {
        return $this->type === 'company';
    }

    /**
     * Повне ім'я або назва компанії.
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->isCompany()) {
            return $this->company_name ?? '';
        }

        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Scope: тільки фізичні особи.
     */
    public function scopeIndividuals($query)
    {
        return $query->where('type', 'individual');
    }

    /**
     * Scope: тільки юридичні особи.
     */
    public function scopeCompanies($query)
    {
        return $query->where('type', 'company');
    }

    /**
     * Пошук донора по номеру рахунку.
     */
    public static function findByBankAccount(string $accountNumber, ?string $bankCode = null): ?self
    {
        $query = DonorBankAccount::where('account_number', $accountNumber);

        if ($bankCode !== null) {
            $query->where('bank_code', $bankCode);
        }

        $donorBankAccount = $query->first();

        return $donorBankAccount?->donor;
    }
}
