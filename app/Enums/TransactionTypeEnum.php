<?php

namespace App\Enums;

enum TransactionTypeEnum: string
{
    case EXPENSE = 'expense'; // gasto
    case INCOME = 'income'; // ingreso

    public static function toArray(): array
    {
        return array_map(function ($value) {
            return $value->value;
        }, self::cases());
    }

    public static function labels(): array
    {
        return [
            self::EXPENSE->value => 'Gasto',
            self::INCOME->value => 'Ingreso',
        ];
    }
}
