<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Servicios básicos',
                'description' => 'Agua, luz, gas, internet',
            ],
            [
                'name' => 'Transporte',
                'description' => 'Gasolina, transporte público, mantenimiento de auto'
            ],
            [
                'name' => 'Alimentación',
                'description' => 'Supermercado, restaurantes',
            ],
            [
                'name' => 'Entretenimiento',
                'description' => 'Netflix, Spotify, etc'
            ],
            [
                'name' => 'Salud',
                'description' => 'Seguros, medicamentos, consultas',
            ],
            [
                'name' => 'Educación',
                'description' => 'Colegiatura, materiales, cursos',
            ],
            [
                'name' => 'Vestimenta',
                'description' => 'Ropa, zapatos, accesorios',
            ],
            [
                'name' => 'Sin categoria',
                'description' => 'Si no sabes en que categoria clasificarlo',
                'is_default' => true,
            ],
        ];

        foreach($categories as $category){
            \App\Models\Category::create($category);
        }
    }
}
