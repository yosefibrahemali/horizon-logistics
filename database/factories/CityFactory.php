<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\City>
 */
class CityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->city,   // اسم المدينة
            'shipping_cost' => $this->faker->randomFloat(2, 10, 200), // تكلفة الشحن
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }
}
