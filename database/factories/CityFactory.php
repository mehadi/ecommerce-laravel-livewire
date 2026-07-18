<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\City>
 */
class CityFactory extends Factory
{
    protected $model = City::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->city();

        return [
            'name' => $name,
            'name_bn' => fake('bn_BD')->city(),
            'is_active' => true,
            'order' => fake()->numberBetween(0, 100),
        ];
    }
}
