<?php

namespace Database\Seeders;

use App\Enums\CourierStatus;
use App\Enums\VehicleType;
use App\Models\Courier;
use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    public function run(): void
    {
        Courier::factory()->count(50)->create();

        Courier::factory()->create([
            'code' => 'CR-000001',
            'name' => 'Budiono Hadi Agung',
            'vehicle_type' => VehicleType::Motorcycle,
            'plate_number' => 'B 1234 XYZ',
            'level' => 3,
            'status' => CourierStatus::Active,
        ]);
    }
}
