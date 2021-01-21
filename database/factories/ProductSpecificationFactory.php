<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductSpecification;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductSpecificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductSpecification::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $product = Product::query()
            ->inRandomOrder()
            ->first();

        return [
            'product_id' => $product->id,
            'name' => ucwords($this->faker->words(rand(1,2), true)),
            'value' => ucwords($this->faker->words(rand(1,3), true)),
        ];
    }
}
