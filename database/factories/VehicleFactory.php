<?php

namespace Database\Factories;

use App\Models\RegisteredUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    public function definition(): array
    {
        $brands = ['Toyota', 'Honda', 'Lexus', 'Mercedes-Benz', 'Kia', 'Hyundai', 'Ford', 'Nissan'];
        $models = ['Corolla', 'Camry', 'Civic', 'Accord', 'RX350', 'C300', 'Rio', 'Elantra', 'Focus', 'Altima'];
        $colors = ['Black', 'Silver', 'White', 'Blue', 'Red', 'Grey'];
        $types = ['Car', 'SUV', 'Van'];

        return [
            'registered_user_id' => RegisteredUser::factory(),
            'plate_number'       => strtoupper(fake()->unique()->bothify('???-###??')),
            'vehicle_brand'      => fake()->randomElement($brands),
            'vehicle_model'      => fake()->randomElement($models),
            'vehicle_color'      => fake()->randomElement($colors),
            'vehicle_type'       => fake()->randomElement($types),
            'registration_date'  => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
