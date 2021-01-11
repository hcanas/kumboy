<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Store::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // 16.402092, 120.566404
        // 16.409573, 120.646220
        $lat_range = [402092, 409573];
        $lng_range = [566404, 646220];

        $coordinates = '16.'.rand($lat_range[0], $lat_range[1]).
                       ','.'120.'.rand($lng_range[0], $lng_range[1]);

        $user = User::query()
            ->inRandomOrder()
            ->first();

        return [
            'user_id' => $user->id,
            'name' => ucwords($this->faker->words(3, true)),
            'contact_number' => '0909'.rand(0000000, 9999999),
            'address' => $this->faker->address,
            'map_coordinates' => $coordinates,
            'map_address' => 'undefined',
            'open_until' => $this->faker->dateTimeBetween('now', '+2 years'),
        ];
    }
}
