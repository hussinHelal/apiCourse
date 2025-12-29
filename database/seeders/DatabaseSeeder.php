<?php

namespace Database\Seeders;

use App\Models\Transactions;
use App\Models\Product;
use App\Models\Categories;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::flushEventListeners();
        Categories::flushEventListeners();
        Product::flushEventListeners();
        Transactions::flushEventListeners();
        // Create users first
        User::factory()->count(200)->create();

        // Create categories first
        Categories::factory()->count(5)->create();

        Product::factory()->count(1000)->create()->each(function(Product $product){
            $categories = Categories::all()->random(mt_rand(1,5))->pluck('id');
            $product->categories()->attach($categories);
        });

        // Create transactions
        Transactions::factory()->count(1000)->create();
    }
}
