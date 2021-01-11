<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $store = Store::query()
            ->inRandomOrder()
            ->first();

        $category = ['Main 1', 'Main 2', 'Main 3', 'Main 4'];

        return [
            'store_id' => $store->id,
            'name' => ucwords($this->faker->words(rand(1,3), true)),
            'qty' => rand(1,100),
            'price' => rand(50, 1000000),
            'main_category' => $category[rand(0,3)],
            'sub_category' => null,
            'sold' => 0,
            'preview' => null,
        ];
    }
}
