<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'description' => fake()->paragraph(1),
            'quantity' => fake()->numberBetween(1,10),
            'status' => fake()->randomElement([Product::AVAILABLE_PRODUCT,Product::UNAVAILABLE_PRODUCT]),
            'image' => fake()->randomElement(['pic1.jpg','pic2.jpg','pic3.jpg']),
            'seller_id' => User::all()->random()->id,
        ];
    }
}
