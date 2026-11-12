<?php

namespace Database\Factories;

use App\Enums\CourierStatus;
use App\Enums\VehicleType;
use App\Models\Courier;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Courier> */
class CourierFactory extends Factory
{
    public function definition(): array
    {
        $vehicleType = fake()->randomElement(VehicleType::cases());

        return [
            'code' => 'CR-'.fake()->unique()->numerify('######'),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '+62'.fake()->unique()->numerify('8##########'),
            'national_id' => fake()->unique()->numerify('################'),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'vehicle_type' => $vehicleType,
            'plate_number' => $vehicleType === VehicleType::Bicycle ? null : strtoupper(fake()->unique()->bothify('B #### ??')),
            'level' => fake()->numberBetween(Courier::MIN_LEVEL, Courier::MAX_LEVEL),
            'status' => fake()->randomElement(CourierStatus::cases()),
            'registered_at' => fake()->dateTimeBetween('-3 years')->format('Y-m-d'),
        ];
    }
}
