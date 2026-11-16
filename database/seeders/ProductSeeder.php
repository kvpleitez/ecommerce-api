<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Teclado Mecánico',
                'description' => 'Teclado mecánico RGB para computadora.',
                'price' => 39.99,
                'stock' => 20,
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Mouse Inalámbrico',
                'description' => 'Mouse óptico inalámbrico.',
                'price' => 19.99,
                'stock' => 30,
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Audífonos Bluetooth',
                'description' => 'Audífonos inalámbricos con conexión Bluetooth.',
                'price' => 29.99,
                'stock' => 15,
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Monitor 24 pulgadas',
                'description' => 'Monitor LED de 24 pulgadas.',
                'price' => 149.99,
                'stock' => 10,
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Webcam HD',
                'description' => 'Cámara web de alta definición.',
                'price' => 34.99,
                'stock' => 12,
                'image' => null,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['name' => $product['name']],
                $product
            );
        }
    }
}
