<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\DeliveryMan;
use App\Models\Shipment;
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

        DeliveryMan::factory(20)->create();

        User::factory(10)->create();
        City::factory(10)->create();

        // توليد 20 شحنة مرتبطة بالمستخدمين والمدن
        Shipment::factory(20)->create();

       

        // User::factory(50)->create();
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
