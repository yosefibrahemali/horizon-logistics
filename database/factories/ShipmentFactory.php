<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipment>
 */
class ShipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sender_id' => User::factory(), // ينشئ مستخدم جديد أو ياخذ id موجود
            'destination_city' => City::factory(), // ينشئ مدينة أو يستخدم id موجود
            'shipment_description' => $this->faker->sentence(6),
            'tracking_number' => strtoupper($this->faker->bothify('TRK-#####')),
            'origin_city' => 'Misrata', // ثابتة حالياً
            'receiver_name' => $this->faker->name,
            'receiver_email' => $this->faker->safeEmail,
            'receiver_phone' => $this->faker->phoneNumber,
            'receiver_address' => $this->faker->address,
            'status' => $this->faker->randomElement(['pending','on_way','delivered','cancelled','returned']),
            'total_weight' => $this->faker->randomFloat(2, 1, 50),
            'shipping_cost' => $this->faker->randomFloat(2, 20, 500),
            'shipment_cost' => $this->faker->randomFloat(2, 20, 500),
            'total_cost' => $this->faker->randomFloat(2, 50, 1000),
            'receive_cost_from' => $this->faker->randomElement(['sender', 'receiver']),
        ];
    }
}
