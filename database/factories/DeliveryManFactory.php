<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeliveryMan>
 */
class DeliveryManFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // ينشئ يوزر جديد ويربطه
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'vehicle_type' => $this->faker->randomElement(['car', 'motorbike', 'bicycle']),
            'vehicle_number' => strtoupper($this->faker->bothify('??###')), // مثال: AB123
            'status' => $this->faker->randomElement(['active', 'inactive', 'suspended']),
            'city_id' => City::inRandomOrder()->first()?->id ?? City::factory(), 
        ];
    }
}
