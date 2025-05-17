<?php

namespace App\Models;

use App\Enums\TransactionTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = [
        'name',
        'description',
        'balance',
        'is_default',
        'user_id',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function actualBalance(bool $save = false): float
    {
        $incomes = $this->transactions()
            ->where('type', TransactionTypeEnum::INCOME->value)
            ->sum('amount');


        $expenses = $this->transactions()
            ->where('type', TransactionTypeEnum::EXPENSE->value)
            ->sum('amount');

        $balance = floatval($incomes) - floatval($expenses);

        if ($save) {
            $this->balance = $balance;
            $this->save();
        }

        return $balance;
    }
}
