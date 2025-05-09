<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::create([
            'name' => 'John Doe',
            'email' => 'john@app.com',
            'password' => Hash::make('password'),
        ]);

        $user = \App\Models\User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@app.com',
            'password' => Hash::make('password'),
        ]);
    }
}
