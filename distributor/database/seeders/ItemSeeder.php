<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Item::create([
            'name' => 'Sample Item 1',
            'category' => 'Category A',
            'stock' => 100,
            'unit' => 'pcs',
            'price' => 10000,
            'minimum_stock' => 10,
        ]);

        Item::create([
            'name' => 'Sample Item 2',
            'category' => 'Category B',
            'stock' => 50,
            'unit' => 'pcs',
            'price' => 20000,
            'minimum_stock' => 5,
        ]);
    }
}
