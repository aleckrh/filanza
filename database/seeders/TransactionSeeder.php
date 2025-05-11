<?php

namespace Database\Seeders;

use App\Enums\TransactionTypeEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $transaction = \App\Models\Transaction::create([
                'name' => 'Transaccion ' . ($i + 1),
                'amount' => rand(20, 100),
                'type' => rand(1, 2) == 1 ? TransactionTypeEnum::EXPENSE->value : TransactionTypeEnum::INCOME->value,
                'category_id' => rand(1, 5),
                'account_id' => 1,
            ]);
        }
    }
}
