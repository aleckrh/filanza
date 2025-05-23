<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'Ahorros',
                'user_id' => 1,
            ],
            [
                'name' => 'Otros',
                'user_id' => 1,
            ],
        ];

        foreach($accounts as $account){
            \App\Models\Account::create($account);
        }
    }
}
