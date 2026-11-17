<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Seeder;

class CategoryBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {
    Category::insert([
        ['name' => 'Electronics'],
        ['name' => 'Clothing'],
        ['name' => 'Furniture'],
    ]);

    Brand::insert([
        ['name' => 'Samsung'],
        ['name' => 'Nike'],
        ['name' => 'Ikea'],
    ]);
}
}
    
    
