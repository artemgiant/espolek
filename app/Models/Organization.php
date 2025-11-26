<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'street',
        'city',
        'postal_code',
        'country',
        'ico',
        'vat_payer',
        'dic',
        'data_box',
        'email',
        'website',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'vat_payer' => 'boolean',
        ];
    }

    /**
     * Get all bank accounts for the organization.
     */
    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    /**
     * Get all transactions through bank accounts.
     */
    public function transactions(): HasMany
    {
        return $this->hasManyThrough(Transaction::class, BankAccount::class);
    }
}
