<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Seller;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transactions>
 */
class TransactionsFactory extends Factory
{

    public function definition(): array
    {

        $seller = Seller::has('product')->get()->random();
        $buyer = User::all()->except($seller->id)->random();

        return [
            'quantity' => fake()->numberBetween(1,10),
            'buyer_id' => $buyer->id,
            'product_id' => $seller->product->random()->id,
        ];
    }
}
