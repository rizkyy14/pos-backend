<?php

namespace Database\Seeders;

use App\Models\Product;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Kopi Susu Gula Aren',
                'sku' => 'MNM-001',
                'price' => 18000,
                'stock' => 100,
                'category' => 'Minuman',
                'is_active' => true,
            ],
            [
                'name' => 'Americano',
                'sku' => 'MNM-002',
                'price' => 15000,
                'stock' => 100,
                'category' => 'Minuman',
                'is_active' => true,
            ],
            [
                'name' => 'Roti Bakar Coklat',
                'sku' => 'MKN-001',
                'price' => 20000,
                'stock' => 50,
                'category' => 'Makanan',
                'is_active' => true,
            ],
            [
                'name' => 'Kentang Goreng',
                'sku' => 'MKN-002',
                'price' => 22000,
                'stock' => 40,
                'category' => 'Makanan',
                'is_active' => true,
            ],
            [
                'name' => 'Air Mineral 600ml',
                'sku' => 'MNM-003',
                'price' => 5000,
                'stock' => 200,
                'category' => 'Minuman',
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
